"""Loading libraries is sandbox start-up, not the learner's running time.

A warm standby container has numpy, pandas, matplotlib and friends resident
before a job arrives, so the warm path never showed this. The classic and
local paths start a fresh interpreter per Run, and the wall clock was started
before the program's own imports - so "import pandas" and "import seaborn"
were charged to the learner. On Windows, where every file the interpreter
opens is scanned before it is handed over, that alone can consume the whole
limit, and the IDE then reports

    Your program was still running after 20 seconds, so the sandbox stopped it.
    A loop that never ends ... is the usual cause.

for a program that had not yet run a line.

Run with:  python -m unittest tests/Python/test_library_preload.py
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
DATASETS = PROJECT_ROOT / "docker/python-runner/seaborn-data"


def run(source: str) -> subprocess.CompletedProcess:
    directory = tempfile.mkdtemp(prefix="ds-preload-")
    workspace = Path(directory) / "workspace"
    workspace.mkdir()
    (workspace / "main.py").write_text(source, encoding="utf-8")

    environment = os.environ.copy()
    environment.update({
        "DS_WORKSPACE": str(workspace),
        "DS_INPUT": str(Path(directory) / "input"),
        "DS_CPU_SECONDS": "120",
        "DS_WALL_SECONDS": "60",
        "DS_MAX_OUTPUT_BYTES": "400000",
        "SEABORN_DATA": str(DATASETS),
        "MPLCONFIGDIR": str(Path(directory) / "mpl"),
    })

    return subprocess.run(
        [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
        capture_output=True, text=True, timeout=300, env=environment, cwd=str(workspace),
    )


class LibraryPreload(unittest.TestCase):
    def test_a_declared_library_is_already_loaded_when_the_program_starts(self) -> None:
        try:
            import pandas  # noqa: F401
        except ModuleNotFoundError:
            self.skipTest("pandas is not installed on this machine.")

        # sys.modules is blocked for learner code, so the observation is made
        # by timing: an already-resident import returns immediately, while a
        # real one costs seconds.
        completed = run(
            "import time\n"
            "start = time.time()\n"
            "import pandas as pd\n"
            "print('IMPORT_SECONDS: %.3f' % (time.time() - start))\n"
            "print(pd.DataFrame({'a': [1, 2]}).shape)\n"
        )

        self.assertIn("IMPORT_SECONDS", completed.stdout, completed.stderr[-1500:])

        seconds = float(
            completed.stdout.split("IMPORT_SECONDS:")[1].split()[0]
        )

        # What "fast" means depends entirely on the machine: this Linux image
        # imports pandas in a fraction of a second once the file cache is warm,
        # while a Windows host with on-access scanning takes seconds. So the
        # cost of a genuine cold import is measured here and compared against,
        # rather than a number picked in advance.
        baseline = subprocess.run(
            [sys.executable, "-c",
             "import time; start = time.time(); import pandas; "
             "print(time.time() - start)"],
            capture_output=True, text=True, timeout=300,
        )
        cold = float(baseline.stdout.strip() or "0")

        if cold < 0.25:
            self.skipTest(
                f"A cold import costs only {cold:.2f}s here, so timing cannot "
                "tell the two paths apart on this machine."
            )

        self.assertLess(
            seconds,
            cold / 2,
            f"import pandas cost the learner {seconds:.2f}s against a cold cost "
            f"of {cold:.2f}s; it should already be resident before their clock starts.",
        )

    def test_a_program_that_needs_nothing_heavy_is_not_slowed_down(self) -> None:
        # Preloading everything unconditionally would punish a one-line
        # program, so only what the source names is loaded.
        import time

        started = time.monotonic()
        completed = run("print('hello')\n")
        elapsed = time.monotonic() - started

        self.assertIn("hello", completed.stdout)
        self.assertLess(
            elapsed,
            10.0,
            "A trivial program must not pay for libraries it never mentions.",
        )

    def test_the_preload_happens_before_the_clock_starts(self) -> None:
        source = RUNNER.read_text(encoding="utf-8")

        ordering = source.find(
            "preload_declared_libraries(script_path)\n"
            "        build_font_index_once()\n"
            "        start_wall_clock"
        )

        self.assertNotEqual(
            -1,
            ordering,
            "Libraries must be loaded before start_wall_clock(), or the learner "
            "is charged for importing them.",
        )

    def test_a_missing_library_still_reports_itself_normally(self) -> None:
        # The preload swallows failures on purpose; the program's own import
        # must still raise so the learner gets the real message.
        completed = run("import tensorflow as tf\nprint(tf.__version__)\n")

        self.assertIn("not installed in the learning sandbox", completed.stderr)
        self.assertIn("ModuleNotFoundError", completed.stderr)

    def test_a_program_that_imports_nothing_heavy_still_runs(self) -> None:
        completed = run("print(sum(range(10)))\n")

        self.assertIn("45", completed.stdout)
        self.assertEqual(0, completed.returncode, completed.stderr[-1000:])


if __name__ == "__main__":
    unittest.main()
