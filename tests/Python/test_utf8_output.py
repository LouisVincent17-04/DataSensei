"""What a program prints must leave the runner as UTF-8.

Laravel turns stdout into JSON and json_encode refuses anything else. On
Windows a pipe is opened in the console code page, so a lesson printing
"Mean: 0.850 ± 0.050" sent the single byte 0xB1 and the IDE reported

    Request failed: Malformed UTF-8 characters, possibly incorrectly encoded

The runner now reconfigures its streams to UTF-8 regardless of how the pipe
was opened. The worst case is forced here with PYTHONIOENCODING=cp1252.

Run with:  python -m unittest tests/Python/test_utf8_output.py
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


def run(source: str, **extra_env: str) -> bytes:
    directory = tempfile.mkdtemp(prefix="ds-utf8-")
    workspace = Path(directory) / "workspace"
    workspace.mkdir()
    (workspace / "main.py").write_text(source, encoding="utf-8")

    environment = os.environ.copy()
    environment.pop("PYTHONUTF8", None)
    environment.update({
        "DS_WORKSPACE": str(workspace),
        "DS_INPUT": str(Path(directory) / "input"),
        "DS_CPU_SECONDS": "60",
        "DS_WALL_SECONDS": "30",
        "DS_MAX_OUTPUT_BYTES": "200000",
        **extra_env,
    })

    completed = subprocess.run(
        [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
        capture_output=True, timeout=120, env=environment, cwd=str(workspace),
    )
    return completed.stdout


class Utf8Output(unittest.TestCase):
    def test_a_non_ascii_character_survives_a_cp1252_pipe(self) -> None:
        out = run('print("Mean: 0.850 \\u00b1 0.050")\n', PYTHONIOENCODING="cp1252")

        decoded = out.decode("utf-8")  # raises if a stray cp1252 byte got through
        self.assertIn("Mean: 0.850 ± 0.050", decoded)

    def test_stderr_is_utf8_as_well(self) -> None:
        directory = tempfile.mkdtemp(prefix="ds-utf8-")
        workspace = Path(directory) / "workspace"
        workspace.mkdir()
        (workspace / "main.py").write_text('raise ValueError("bad value \\u2192 here")\n', encoding="utf-8")

        environment = os.environ.copy()
        environment.update({
            "DS_WORKSPACE": str(workspace),
            "DS_INPUT": str(Path(directory) / "input"),
            "DS_CPU_SECONDS": "60",
            "DS_WALL_SECONDS": "30",
            "DS_MAX_OUTPUT_BYTES": "200000",
            "PYTHONIOENCODING": "cp1252",
        })
        completed = subprocess.run(
            [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
            capture_output=True, timeout=120, env=environment, cwd=str(workspace),
        )

        decoded = completed.stderr.decode("utf-8")
        self.assertIn("bad value → here", decoded)

    def test_a_character_the_pipe_cannot_hold_is_replaced_not_fatal(self) -> None:
        # With the streams forced to UTF-8 everything is representable; this
        # pins that printing something exotic never raises inside the runner.
        out = run('print("emoji \\U0001F600 and CJK \\u6f22\\u5b57")\n', PYTHONIOENCODING="ascii")

        decoded = out.decode("utf-8")
        self.assertIn("emoji", decoded)
        self.assertIn("漢字", decoded)


if __name__ == "__main__":
    unittest.main()
