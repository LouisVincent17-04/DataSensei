"""The two defects behind "Execution stopped after 10 seconds".

1. One preloaded library missing from the runner image stopped every standby
   container from reporting READY, so the warm pool stayed empty and every run
   fell back to the slow "docker run" path.
2. The learner's time limit was measured by Laravel around the whole docker
   invocation, so container start-up and "import pandas" were billed to the
   learner and a healthy program was reported as an infinite loop.

Run with:  python -m unittest tests/Python/test_sandbox_speed_and_limits.py
"""

from __future__ import annotations

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

sys.path.insert(0, str(Path(__file__).resolve().parent))
from test_warm_standby_runner import Standby  # noqa: E402


def ready_marker_appeared(standby: Standby, timeout: float = 30.0) -> bool:
    deadline = time.monotonic() + timeout
    while time.monotonic() < deadline:
        text = standby.stderr_path.read_text(encoding="utf-8", errors="ignore")
        if "__DATASENSEI_READY__" in text:
            return True
        if "__DATASENSEI_PRELOAD_FAILED__" in text:
            return False
        if standby.process.poll() is not None:
            return "__DATASENSEI_READY__" in standby.stderr_path.read_text(
                encoding="utf-8", errors="ignore"
            )
        time.sleep(0.05)
    return False


class PreloadResilienceTest(unittest.TestCase):
    def test_a_library_the_image_does_not_have_still_leaves_the_container_ready(self) -> None:
        # "seaborn" stands in for any package added to the Dockerfile after the
        # instructor last rebuilt their image.
        standby = Standby(preload="json,definitely_not_installed_xyz,base64")
        self.addCleanup(standby.close)

        self.assertTrue(
            ready_marker_appeared(standby),
            "A missing preload must not stop the container joining the pool:\n"
            + standby.stderr_path.read_text(encoding="utf-8", errors="ignore"),
        )

        stderr = standby.stderr_path.read_text(encoding="utf-8", errors="ignore")
        self.assertIn("__DATASENSEI_PRELOAD_SKIPPED__", stderr)
        self.assertIn("definitely_not_installed_xyz", stderr)
        self.assertNotIn("__DATASENSEI_PRELOAD_FAILED__", stderr)

        # And it still runs a job.
        reply = standby.start({"main.py": "print('alive')\n"}, "main.py", interactive=False)
        self.assertEqual(reply["state"], "exited")
        self.assertIn("alive", reply["stdout"])

    def test_a_half_imported_library_still_refuses_the_container(self) -> None:
        # A module that raises during import leaves a partially initialised C
        # extension behind; such a container must never take a learner's job.
        broken = Path(tempfile.mkdtemp(prefix="ds-broken-preload-"))
        self.addCleanup(shutil.rmtree, broken, True)
        (broken / "ds_broken_module.py").write_text("raise RuntimeError('boom')\n", encoding="utf-8")

        previous = os.environ.get("PYTHONPATH")
        os.environ["PYTHONPATH"] = str(broken) + (os.pathsep + previous if previous else "")
        try:
            standby = Standby(preload="ds_broken_module")
            self.addCleanup(standby.close)
            self.assertFalse(ready_marker_appeared(standby, timeout=15.0))
            self.assertIn(
                "__DATASENSEI_PRELOAD_FAILED__",
                standby.stderr_path.read_text(encoding="utf-8", errors="ignore"),
            )
        finally:
            if previous is None:
                os.environ.pop("PYTHONPATH", None)
            else:
                os.environ["PYTHONPATH"] = previous


class LearnerTimeLimitTest(unittest.TestCase):
    """The clock must start at the learner's first statement."""

    def run_classic(self, source: str, wall_seconds: int, sleep_before: float = 0.0):
        # resolve(): the runner compares real paths, and a tmp dir can be a symlink.
        root = Path(tempfile.mkdtemp(prefix="ds-wall-")).resolve()
        self.addCleanup(shutil.rmtree, root, True)
        workspace = root / "workspace"
        workspace.mkdir()
        (workspace / "main.py").write_text(source, encoding="utf-8")
        runner = root / "datasensei_runner.py"
        shutil.copy2(RUNNER, runner)

        environment = os.environ.copy()
        environment.update({
            "DS_WORKSPACE": str(workspace),
            "DS_INPUT": str(root / "no-input"),
            "DS_WALL_SECONDS": str(wall_seconds),
            "DS_CPU_SECONDS": "120",
            "DS_MAX_OUTPUT_BYTES": "200000",
        })

        if sleep_before:
            time.sleep(sleep_before)

        started = time.monotonic()
        completed = subprocess.run(
            [sys.executable, "-S", "-B", str(runner), str(workspace / "main.py")],
            capture_output=True, text=True, timeout=wall_seconds + 45, check=False,
            env=environment,
        )
        return completed, time.monotonic() - started

    def test_an_endless_loop_is_stopped_with_a_message_that_names_the_limit(self) -> None:
        completed, elapsed = self.run_classic("print('go')\nwhile True:\n    pass\n", 3)

        self.assertEqual(completed.returncode, 124)
        self.assertIn("__DATASENSEI_TIME_LIMIT__", completed.stderr)
        self.assertIn("still running after 3 seconds", completed.stderr)
        self.assertLess(elapsed, 20, "the watchdog must stop the program near its own limit")

    def test_slow_start_up_is_not_charged_to_the_learner(self) -> None:
        # The program sleeps for most of a 4 second budget and must survive:
        # only its own running time counts, not anything before it started.
        completed, _ = self.run_classic("import time\ntime.sleep(2.0)\nprint('finished')\n", 4)

        self.assertEqual(completed.returncode, 0, completed.stderr)
        self.assertIn("finished", completed.stdout)
        self.assertNotIn("__DATASENSEI_TIME_LIMIT__", completed.stderr)

    def test_waiting_for_an_answer_does_not_use_the_budget(self) -> None:
        standby = Standby()
        self.addCleanup(standby.close)

        source = "name = input('Name: ')\nprint('hi', name)\n"
        reply = standby.start({"main.py": source}, "main.py", idle=30, wall_seconds=3)
        self.assertEqual(reply["state"], "waiting")

        # Think for longer than the whole budget before answering.
        time.sleep(4.0)
        reply = standby.feed("Louis", int(reply.get("seq", 0)))

        self.assertEqual(reply["state"], "exited", reply)
        self.assertIn("hi Louis", reply["stdout"])
        self.assertNotIn("__DATASENSEI_TIME_LIMIT__", reply.get("stderr", ""))


if __name__ == "__main__":
    unittest.main()
