from __future__ import annotations

import base64
import os
import shutil
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path


PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"
INPUT_MARKER = "__DATASENSEI_INPUT_REQUIRED__:"
CONSUMED_MARKER = "__DATASENSEI_INPUTS_CONSUMED__:"


class InteractiveInputRunnerTest(unittest.TestCase):
    def run_program(self, source: str, stdin: str = "") -> subprocess.CompletedProcess[str]:
        with tempfile.TemporaryDirectory(prefix="datasensei-input-test-") as directory:
            test_root = Path(directory)
            workspace = test_root / "workspace"
            workspace.mkdir()
            script = workspace / "practice.py"
            script.write_text(source, encoding="utf-8")
            runner = test_root / "datasensei_runner.py"
            shutil.copy2(RUNNER, runner)

            environment = os.environ.copy()
            environment.update(
                {
                    "DS_WORKSPACE": str(workspace),
                    "DS_INPUT": str(workspace),
                    "DS_INTERACTIVE_INPUT": "1",
                    "DS_CPU_SECONDS": "5",
                    "DS_MAX_OUTPUT_BYTES": "200000",
                }
            )

            return subprocess.run(
                [sys.executable, "-B", "-u", str(runner), str(script)],
                input=stdin,
                text=True,
                capture_output=True,
                timeout=10,
                check=False,
                env=environment,
            )

    def decoded_prompt(self, stderr: str) -> str | None:
        for line in stderr.splitlines():
            if line.startswith(INPUT_MARKER):
                encoded = line.removeprefix(INPUT_MARKER)
                return base64.b64decode(encoded).decode("utf-8")

        return None

    def consumed_count(self, stderr: str) -> int | None:
        for line in stderr.splitlines():
            if line.startswith(CONSUMED_MARKER):
                return int(line.removeprefix(CONSUMED_MARKER))

        return None

    def test_consumed_count_is_zero_when_stdin_is_empty(self) -> None:
        result = self.run_program('name = input("Name: ")\n')

        self.assertEqual(75, result.returncode)
        self.assertEqual(0, self.consumed_count(result.stderr))

    def test_consumed_count_reports_answers_read_before_the_next_prompt(self) -> None:
        """The IDE compares this with what it sent; a lower number means stdin
        never reached the program, which is the infinite-prompt symptom."""
        result = self.run_program(
            'first = input("First: ")\n'
            'second = input("Second: ")\n'
            'third = input("Third: ")\n',
            "one\ntwo\n",
        )

        self.assertEqual(75, result.returncode)
        self.assertEqual(2, self.consumed_count(result.stderr))
        self.assertEqual("Third: ", self.decoded_prompt(result.stderr))

    def test_missing_input_returns_exact_prompt_without_eof_traceback(self) -> None:
        result = self.run_program('name = input("Enter your name: ")\nprint(name)\n')

        self.assertEqual(75, result.returncode)
        self.assertEqual("Enter your name: ", self.decoded_prompt(result.stderr))
        self.assertIn("Enter your name:", result.stdout)
        self.assertNotIn("EOFError", result.stderr)
        self.assertNotIn("Traceback", result.stderr)

    def test_multiple_inputs_complete_in_their_original_order(self) -> None:
        result = self.run_program(
            'name = input("Enter your name: ")\n'
            'age = input("Enter your age: ")\n'
            'course = input("Enter your course: ")\n'
            'print(f"Student: {name} | Age: {age} | Course: {course}")\n',
            "Ada\n21\nData Science\n",
        )

        self.assertEqual(0, result.returncode)
        self.assertIn("Enter your name:", result.stdout)
        self.assertIn("Enter your age:", result.stdout)
        self.assertIn("Enter your course:", result.stdout)
        self.assertIn("Student: Ada | Age: 21 | Course: Data Science", result.stdout)
        self.assertIsNone(self.decoded_prompt(result.stderr))
        self.assertNotIn("EOFError", result.stderr)

    def test_prior_answer_is_retained_when_the_next_prompt_needs_input(self) -> None:
        result = self.run_program(
            'name = input("Name: ")\n'
            'print(f"Hello, {name}")\n'
            'section = input("Section: ")\n',
            "Mina\n",
        )

        self.assertEqual(75, result.returncode)
        self.assertIn("Hello, Mina", result.stdout)
        self.assertEqual("Section: ", self.decoded_prompt(result.stderr))
        self.assertNotIn("Traceback", result.stderr)

    def test_prompt_marker_stays_parseable_after_unterminated_stderr(self) -> None:
        result = self.run_program(
            'import sys\n'
            'sys.stderr.write("warning before input")\n'
            'value = input("Value: ")\n'
        )

        self.assertEqual(75, result.returncode)
        self.assertIn("warning before input", result.stderr)
        self.assertEqual("Value: ", self.decoded_prompt(result.stderr))
        self.assertNotIn("Traceback", result.stderr)


if __name__ == "__main__":
    unittest.main()
