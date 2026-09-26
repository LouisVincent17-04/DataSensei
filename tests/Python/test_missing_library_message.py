"""A lesson example that needs an uninstalled library must say so plainly.

Several modules show code written against TensorFlow, PyTorch, Spark, Kafka,
cloud SDKs and database clients. Those need a GPU, a cluster, a server or a
network that the learning sandbox deliberately does not provide, so the code
is there to read rather than to run. Until now the learner got a bare
ModuleNotFoundError traceback, which reads as "you broke it".

Run with:  python -m unittest tests/Python/test_missing_library_message.py
"""

from __future__ import annotations

import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"


def run(source: str) -> subprocess.CompletedProcess:
    directory = tempfile.mkdtemp(prefix="ds-missing-lib-")
    workspace = Path(directory) / "workspace"
    workspace.mkdir()
    (workspace / "main.py").write_text(source, encoding="utf-8")

    environment = os.environ.copy()
    environment.update({
        "DS_WORKSPACE": str(workspace),
        "DS_INPUT": str(Path(directory) / "input"),
        "DS_CPU_SECONDS": "60",
        "DS_WALL_SECONDS": "30",
        "DS_MAX_OUTPUT_BYTES": "200000",
    })

    return subprocess.run(
        [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
        capture_output=True, text=True, timeout=120, env=environment, cwd=str(workspace),
    )


class MissingLibraryMessage(unittest.TestCase):
    def test_a_known_library_is_named_in_plain_language(self) -> None:
        completed = run("import tensorflow as tf\nprint(tf.__version__)\n")

        self.assertIn("TensorFlow", completed.stderr)
        self.assertIn("not installed in the learning sandbox", completed.stderr)
        self.assertIn(
            "Nothing is wrong with what you wrote",
            completed.stderr,
            "The learner must be told their own code is not at fault.",
        )

    def test_an_unknown_library_still_gets_a_useful_sentence(self) -> None:
        completed = run("import some_library_nobody_has\n")

        self.assertIn("some_library_nobody_has", completed.stderr)
        self.assertIn("not installed in the learning sandbox", completed.stderr)

    def test_the_traceback_is_still_there_underneath(self) -> None:
        completed = run("import tensorflow\n")

        self.assertIn("Traceback", completed.stderr)
        self.assertIn("ModuleNotFoundError", completed.stderr)

    def test_an_ordinary_mistake_is_not_dressed_up_as_a_missing_library(self) -> None:
        completed = run("print(1 / 0)\n")

        self.assertNotIn("not installed in the learning sandbox", completed.stderr)
        self.assertIn("ZeroDivisionError", completed.stderr)

    def test_a_missing_sibling_import_is_not_blamed_on_the_sandbox(self) -> None:
        # A learner importing their own file that does not exist yet gets the
        # generic sentence, not a claim about a famous library.
        completed = run("import my_helper\n")

        self.assertIn("my_helper", completed.stderr)
        self.assertNotIn("deep-learning", completed.stderr)


if __name__ == "__main__":
    unittest.main()
