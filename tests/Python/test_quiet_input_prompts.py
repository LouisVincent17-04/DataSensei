"""Graded runs must not print input() prompts.

Coding challenges compare stdout exactly with the expected output. The lessons
teach input("Enter your name: "), and Python writes that prompt to stdout, so a
correct program failed every stdin test case with output like

    Enter a: Enter b: 5

when "5" was expected. Graded runs now ask the runner not to print prompts.
The IDE does not ask, so prompts still appear there.

Both routes into the runner are covered: the environment variable used by the
classic and local paths, and the job field used by warm standby containers,
which were started before the grader's request existed.

Run with:  python -m unittest tests/Python/test_quiet_input_prompts.py
"""

from __future__ import annotations

import base64
import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"

sys.path.insert(0, str(Path(__file__).resolve().parent))
from test_warm_standby_runner import Standby  # noqa: E402

PROGRAM = "a = int(input('Enter a: '))\nb = int(input('Enter b: '))\nprint(a + b)\n"


def run_classic(source: str, stdin: str, **extra_env: str) -> subprocess.CompletedProcess:
    directory = tempfile.mkdtemp(prefix="ds-quiet-")
    workspace = Path(directory) / "workspace"
    workspace.mkdir()
    (workspace / "main.py").write_text(source, encoding="utf-8")

    environment = os.environ.copy()
    environment.pop("DS_QUIET_INPUT_PROMPTS", None)
    environment.pop("DS_INTERACTIVE_INPUT", None)
    environment.update({
        "DS_WORKSPACE": str(workspace),
        "DS_INPUT": str(Path(directory) / "input"),
        "DS_CPU_SECONDS": "30",
        "DS_WALL_SECONDS": "20",
        "DS_MAX_OUTPUT_BYTES": "100000",
        **extra_env,
    })

    return subprocess.run(
        [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
        input=stdin, capture_output=True, text=True, timeout=120, env=environment, cwd=str(workspace),
    )


class QuietPromptsClassicPath(unittest.TestCase):
    def test_a_graded_run_prints_only_the_answer(self) -> None:
        completed = run_classic(PROGRAM, "2\n3\n", DS_QUIET_INPUT_PROMPTS="1")

        self.assertEqual("5", completed.stdout.strip(), completed.stderr[-1500:])

    def test_without_the_flag_prompts_are_printed_as_before(self) -> None:
        completed = run_classic(PROGRAM, "2\n3\n")

        self.assertEqual("Enter a: Enter b: 5", completed.stdout.strip())

    def test_the_ide_still_shows_prompts(self) -> None:
        # Interactive IDE mode, which never sets the quiet flag.
        completed = run_classic(PROGRAM, "2\n3\n", DS_INTERACTIVE_INPUT="1")

        self.assertIn("Enter a: ", completed.stdout)
        self.assertIn("5", completed.stdout)

    def test_input_without_a_prompt_is_unaffected(self) -> None:
        completed = run_classic("print(int(input()) * 2)\n", "21\n", DS_QUIET_INPUT_PROMPTS="1")

        self.assertEqual("42", completed.stdout.strip())

    def test_ordinary_prints_are_never_suppressed(self) -> None:
        completed = run_classic(
            "print('Sum:', int(input('Enter a: ')) + 1)\n", "4\n", DS_QUIET_INPUT_PROMPTS="1",
        )

        self.assertEqual("Sum: 5", completed.stdout.strip(), "Only the prompt is quiet, never the output.")

    def test_running_out_of_input_still_raises_normally(self) -> None:
        completed = run_classic(PROGRAM, "2\n", DS_QUIET_INPUT_PROMPTS="1")

        self.assertIn("EOFError", completed.stderr)
        self.assertNotIn("Enter", completed.stdout)


class QuietPromptsWarmPath(unittest.TestCase):
    def setUp(self) -> None:
        self.standby = Standby()

    def tearDown(self) -> None:
        self.standby.close()

    def start(self, quiet: bool) -> dict:
        job = {
            "entry": "main.py",
            "dirs": [],
            "files": [{"path": "main.py", "b64": base64.b64encode(PROGRAM.encode("utf-8")).decode("ascii")}],
            "stdin": "2\n3\n",
            "interactive": False,
            "quiet_prompts": quiet,
            "cpu_seconds": 5,
            "wall_seconds": 5,
            "idle_seconds": 20,
        }
        return self.standby.attach({"op": "start", "job": job, "wait": 8, "pickup": 8})

    def test_a_warm_graded_job_prints_only_the_answer(self) -> None:
        reply = self.start(quiet=True)

        self.assertEqual("exited", reply["state"], reply)
        self.assertEqual("5\n", reply["stdout"])


class QuietPromptsWarmPathDefault(unittest.TestCase):
    def setUp(self) -> None:
        self.standby = Standby()

    def tearDown(self) -> None:
        self.standby.close()

    def test_a_warm_job_without_the_field_keeps_prompts(self) -> None:
        job = {
            "entry": "main.py",
            "dirs": [],
            "files": [{"path": "main.py", "b64": base64.b64encode(PROGRAM.encode("utf-8")).decode("ascii")}],
            "stdin": "2\n3\n",
            "interactive": False,
            "cpu_seconds": 5,
            "wall_seconds": 5,
            "idle_seconds": 20,
        }
        reply = self.standby.attach({"op": "start", "job": job, "wait": 8, "pickup": 8})

        self.assertIn("Enter a: ", reply["stdout"])


if __name__ == "__main__":
    unittest.main()
