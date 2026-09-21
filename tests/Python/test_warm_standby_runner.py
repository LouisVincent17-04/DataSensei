from __future__ import annotations

import base64
import json
import os
import shutil
import subprocess
import sys
import tempfile
import time
import unittest
from pathlib import Path


PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"


class Standby:
    """One runner in --standby mode, driven the way PythonWarmSandbox drives it."""

    def __init__(self, preload: str = "") -> None:
        self.root = Path(tempfile.mkdtemp(prefix="datasensei-standby-test-"))
        self.workspace = self.root / "workspace"
        self.session = self.root / "session"
        self.workspace.mkdir()
        runner = self.root / "datasensei_runner.py"
        shutil.copy2(RUNNER, runner)
        self.stdout_path = self.root / "stdout.txt"
        self.stderr_path = self.root / "stderr.txt"

        environment = os.environ.copy()
        environment.update(
            {
                "DS_WORKSPACE": str(self.workspace),
                "DS_INPUT": str(self.root / "no-input-mount"),
                "DS_SESSION_DIR": str(self.session),
                "DS_STANDBY_SECONDS": "30",
                "DS_MAX_OUTPUT_BYTES": "200000",
                "DS_PRELOAD": preload,
            }
        )
        self.process = subprocess.Popen(
            [sys.executable, "-B", "-u", str(runner), "--standby"],
            stdin=subprocess.DEVNULL,
            stdout=open(self.stdout_path, "wb"),
            stderr=open(self.stderr_path, "wb"),
            env=environment,
        )

    def attach(self, request: dict, timeout: float = 25.0) -> dict:
        """Exactly what Laravel does with "docker exec -i <container> python ... --attach"."""
        environment = os.environ.copy()
        environment["DS_SESSION_DIR"] = str(self.session)
        self.session.mkdir(parents=True, exist_ok=True)
        completed = subprocess.run(
            [sys.executable, "-S", "-B", str(self.root / "datasensei_runner.py"), "--attach"],
            input=json.dumps(request), text=True, capture_output=True, timeout=timeout, env=environment, check=False,
        )
        return json.loads(completed.stdout)

    def start(self, files: dict[str, str], entry: str, stdin: str = "", interactive: bool = True, idle: int = 20, **extra) -> dict:
        job = {
            "entry": entry,
            "dirs": [],
            "files": [
                {"path": path, "b64": base64.b64encode(content.encode("utf-8")).decode("ascii")}
                for path, content in files.items()
            ],
            "stdin": stdin,
            "interactive": interactive,
            "cpu_seconds": 5,
            "wall_seconds": extra.pop("wall_seconds", 5),
            "idle_seconds": idle,
        }
        return self.attach({"op": "start", "job": job, "wait": 8, "pickup": 8, **extra})

    def feed(self, line: str, seq: int) -> dict:
        return self.attach({"op": "feed", "lines": line + "\n", "seq": seq, "wait": 8})

    def close(self) -> None:
        if self.process.poll() is None:
            self.process.kill()
        self.process.wait(timeout=5)
        shutil.rmtree(self.root, ignore_errors=True)


class WarmStandbyRunnerTest(unittest.TestCase):
    def setUp(self) -> None:
        self.standby = Standby()
        self.addCleanup(self.standby.close)

    def test_program_continues_after_each_answer_without_being_replayed(self) -> None:
        source = (
            "print('started')\n"
            "open('runs.txt', 'a').write('x')\n"
            "name = input('Name: ')\n"
            "age = int(input('Age: '))\n"
            "print(name, age + 1, len(open('runs.txt').read()))\n"
        )
        first = self.standby.start({"main.py": source}, "main.py")
        self.assertEqual("waiting", first["state"])
        self.assertEqual("Name: ", first["prompt"])
        self.assertEqual(0, first["consumed"])
        # The prompt has no newline and is still delivered, exactly as printed.
        self.assertEqual("started\nName: ", first["stdout"])
        self.assertIsNone(self.standby.process.poll(), "the program must stay alive while it waits")

        second = self.standby.feed("Louis", first["seq"])
        self.assertEqual("waiting", second["state"])
        self.assertEqual("Age: ", second["prompt"])
        self.assertEqual(1, second["consumed"])
        self.assertEqual("started\nName: Age: ", second["stdout"])

        third = self.standby.feed("20", second["seq"])
        self.assertEqual("exited", third["state"])
        self.assertEqual(0, third["exit_code"])
        # "1": the file was appended once, so the program ran exactly once.
        self.assertEqual("started\nName: Age: Louis 21 1\n", third["stdout"])
        self.assertEqual("", third["stderr"])
        self.assertEqual(0, self.standby.process.wait(timeout=10))

    def test_a_container_can_be_claimed_only_once(self) -> None:
        first = self.standby.start({"main.py": "input('Waiting: ')\n"}, "main.py")
        self.assertEqual("waiting", first["state"])
        second = self.standby.start({"main.py": "print('intruder')\n"}, "main.py")
        self.assertEqual("taken", second["state"])

    def test_sibling_modules_and_folders_of_the_job_are_importable(self) -> None:
        reply = self.standby.start(
            {
                "app/main.py": "from helper import twice\nprint(twice(21))\nprint(open('data/n.csv').read().strip())\n",
                "app/helper.py": "def twice(value):\n    return value * 2\n",
                "data/n.csv": "a,b",
            },
            "app/main.py",
            interactive=False,
        )
        # The working directory stays the workspace root, as in a classic run.
        self.assertEqual(("exited", 0, "42\na,b\n"), (reply["state"], reply["exit_code"], reply["stdout"]))

    def test_non_interactive_job_reads_supplied_stdin_then_reaches_eof(self) -> None:
        reply = self.standby.start(
            {"main.py": "print(input())\nprint(input())\nprint(input())\n"}, "main.py", stdin="one\ntwo\n", interactive=False,
        )
        self.assertEqual("exited", reply["state"])
        self.assertEqual(1, reply["exit_code"])
        self.assertEqual("one\ntwo\n", reply["stdout"])
        self.assertIn("EOFError", reply["stderr"])

    def test_errors_and_exit_codes_are_reported(self) -> None:
        reply = self.standby.start({"main.py": "print('before')\nraise ValueError('boom')\n"}, "main.py", interactive=False)
        self.assertEqual(1, reply["exit_code"])
        self.assertEqual("before\n", reply["stdout"])
        self.assertIn("ValueError: boom", reply["stderr"])

    def test_an_endless_program_is_reported_as_timeout_not_forever(self) -> None:
        source = "import time\nprint('go', flush=True)\nwhile True:\n    time.sleep(0.05)\n"
        reply = self.standby.start({"main.py": source}, "main.py", interactive=False, wait=1.5)
        self.assertEqual("timeout", reply["state"])
        self.assertEqual("go\n", reply["stdout"])

    def test_a_job_that_is_never_picked_up_is_not_reported_as_a_timeout(self) -> None:
        # The standby process is gone, so nothing will ever start the job. The
        # helper must say so quickly instead of blaming the learner's program.
        self.standby.process.kill()
        self.standby.process.wait(timeout=5)
        began = time.monotonic()
        reply = self.standby.start({"main.py": "input()\n"}, "main.py", pickup=1)
        self.assertEqual("not_started", reply["state"])
        self.assertLess(time.monotonic() - began, 4)

    def test_job_files_cannot_escape_the_workspace(self) -> None:
        outside = self.standby.root / "escaped.txt"
        reply = self.standby.start(
            {"../escaped.txt": "no", "/abs.txt": "kept inside", "main.py": "print('ok')\n"}, "main.py", interactive=False,
        )
        self.assertEqual(0, reply["exit_code"])
        self.assertFalse(outside.exists())
        self.assertTrue((self.standby.workspace / "abs.txt").exists())

    def test_entry_outside_the_workspace_is_refused(self) -> None:
        reply = self.standby.start({"main.py": "print('ok')\n"}, "../../etc/passwd", interactive=False)
        self.assertEqual(126, reply["exit_code"])

    def test_abandoned_input_ends_the_session_by_itself(self) -> None:
        reply = self.standby.start({"main.py": "input('Waiting: ')\n"}, "main.py", idle=5)
        self.assertEqual("waiting", reply["state"])
        self.assertEqual(75, self.standby.process.wait(timeout=15))

    def test_blocked_imports_are_still_rejected_in_a_job(self) -> None:
        reply = self.standby.start({"main.py": "import subprocess\nprint('never')\n"}, "main.py", interactive=False)
        self.assertEqual(126, reply["exit_code"])
        self.assertNotIn("never", reply["stdout"])

    def test_the_attach_helper_starts_in_milliseconds(self) -> None:
        # It runs on every step, so it must not load the heavy runner module.
        began = time.monotonic()
        reply = self.standby.attach({"op": "nonsense"})
        self.assertEqual("bad_request", reply["state"])
        self.assertLess(time.monotonic() - began, 0.5)


class LazyMatplotlibTest(unittest.TestCase):
    def run_classic(self, source: str, python_flags: list[str] | None = None) -> subprocess.CompletedProcess[str]:
        with tempfile.TemporaryDirectory(prefix="datasensei-lazy-test-") as directory:
            root = Path(directory)
            workspace = root / "workspace"
            workspace.mkdir()
            (workspace / "main.py").write_text(source, encoding="utf-8")
            runner = root / "datasensei_runner.py"
            shutil.copy2(RUNNER, runner)
            environment = os.environ.copy()
            environment.update({"DS_WORKSPACE": str(workspace), "DS_INPUT": str(workspace), "DS_CPU_SECONDS": "10"})

            return subprocess.run(
                [sys.executable, "-B", "-u", *(python_flags or []), str(runner), str(workspace / "main.py")],
                text=True, capture_output=True, timeout=30, check=False, env=environment,
            )

    def test_plain_program_never_imports_matplotlib(self) -> None:
        # -X importtime lists every module the interpreter loads on stderr.
        result = self.run_classic("print('hello')\n", ["-X", "importtime"])
        self.assertEqual(0, result.returncode)
        self.assertIn("hello", result.stdout)
        self.assertNotIn("matplotlib", result.stderr)

    def test_plots_are_still_captured_and_show_is_silent(self) -> None:
        try:
            import matplotlib  # noqa: F401
        except Exception:
            self.skipTest("matplotlib is not installed here")

        result = self.run_classic("import matplotlib.pyplot as plt\nplt.plot([1, 2, 3])\nplt.show()\nprint('done')\n")
        self.assertEqual(0, result.returncode, result.stderr)
        self.assertIn("done", result.stdout)
        self.assertIn("__PLOT_BASE64__:", result.stdout)
        self.assertNotIn("cannot be shown", result.stderr)


if __name__ == "__main__":
    unittest.main()


class SandboxFileAccessTest(unittest.TestCase):
    """What the audit hook lets a lesson program read."""

    run_classic = LazyMatplotlibTest.run_classic

    def test_time_zone_data_is_readable_because_pandas_needs_it_to_import(self) -> None:
        if not Path("/usr/share/zoneinfo/UTC").exists():
            self.skipTest("no system time zone database here")

        result = self.run_classic("from zoneinfo import ZoneInfo\nprint(ZoneInfo('UTC').key)\n")
        self.assertEqual(0, result.returncode, result.stderr)
        self.assertIn("UTC", result.stdout)

    def test_a_learner_program_still_cannot_read_process_or_system_files(self) -> None:
        for path in ("/proc/self/maps", "/proc/cpuinfo", "/etc/passwd"):
            result = self.run_classic(f"print(open({path!r}).read()[:10])\n")
            self.assertNotEqual(0, result.returncode, path)
            self.assertIn("Sandbox policy blocked", result.stderr, path)

    def test_scikit_learn_clustering_runs_when_the_library_is_installed(self) -> None:
        try:
            import sklearn  # noqa: F401
        except Exception:
            self.skipTest("scikit-learn is not installed here")

        # KMeans goes through threadpoolctl, which reads /proc/self/maps and
        # opens the OpenMP/BLAS runtimes; both used to be blocked.
        source = (
            "import numpy as np\nfrom sklearn.cluster import KMeans\n"
            "X = np.random.default_rng(0).random((60, 2))\n"
            "print(KMeans(n_clusters=3, n_init=2, random_state=0).fit(X).labels_.shape)\n"
        )
        result = self.run_classic(source)
        self.assertEqual(0, result.returncode, result.stderr)
        self.assertIn("(60,)", result.stdout)
        self.assertNotIn("serial mode", result.stderr)
