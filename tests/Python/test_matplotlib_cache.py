"""matplotlib must be able to keep its font index between runs.

The image sets MPLCONFIGDIR; the local driver did not, so matplotlib fell back
to a location the sandbox refuses to write. It then rebuilt its font index on
every single Run by scanning every font installed on the machine. On Windows
that is slow enough to spend the learner's whole time limit before their first
line of code executes, and the IDE reported it as

    Your program was still running after 20 seconds, so the sandbox stopped it.

which blames the learner for a cache problem.

Run with:  python -m unittest tests/Python/test_matplotlib_cache.py
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

PLOT = """
import matplotlib.pyplot as plt

figure, axis = plt.subplots()
axis.plot([1, 2, 3], [2, 4, 8])
axis.set_title("cache check")
plt.show()
print("plotted")
"""


def run_with_cache(cache: Path, workspace: Path) -> subprocess.CompletedProcess:
    (workspace / "main.py").write_text(PLOT, encoding="utf-8")

    environment = os.environ.copy()
    environment.update({
        "DS_WORKSPACE": str(workspace),
        "DS_INPUT": str(workspace.parent / "input"),
        "DS_CPU_SECONDS": "120",
        "DS_WALL_SECONDS": "60",
        "DS_MAX_OUTPUT_BYTES": "400000",
        "MPLCONFIGDIR": str(cache),
        "SEABORN_DATA": str(DATASETS),
    })

    return subprocess.run(
        [sys.executable, "-B", "-u", str(RUNNER), str(workspace / "main.py")],
        capture_output=True, text=True, timeout=300, env=environment, cwd=str(workspace),
    )


class MatplotlibCache(unittest.TestCase):
    def setUp(self) -> None:
        try:
            import matplotlib  # noqa: F401
        except ModuleNotFoundError:
            self.skipTest("matplotlib is not installed on this machine.")

        # Deliberately NOT under /tmp: on Linux /tmp is a writable root
        # already, which would hide the defect entirely. This mirrors Windows,
        # where the cache lives somewhere that matches no root.
        self._home = Path(PROJECT_ROOT / "storage/framework/testing" / f"mpl-{os.getpid()}")
        self._home.mkdir(parents=True, exist_ok=True)

    def tearDown(self) -> None:
        import shutil

        shutil.rmtree(self._home, ignore_errors=True)

    def test_matplotlib_can_write_its_cache_outside_the_usual_roots(self) -> None:
        cache = self._home / "cache"
        cache.mkdir()
        workspace = self._home / "workspace"
        workspace.mkdir()

        completed = run_with_cache(cache, workspace)

        self.assertNotIn(
            "blocked",
            completed.stderr,
            "The sandbox refused matplotlib's cache:\n" + completed.stderr[-1500:],
        )
        self.assertIn("plotted", completed.stdout)

        written = [path for path in cache.rglob("*") if path.is_file()]
        self.assertNotEqual(
            [],
            written,
            "matplotlib wrote no cache, so it will rescan every font on the next run.",
        )

    def test_the_font_index_is_built_before_the_learners_clock_starts(self) -> None:
        """Ordering, asserted on the source.

        The cost itself cannot be measured here: this image has a few dozen
        fonts and the scan is fast, while on Windows it can outlast the whole
        time limit. What decides the outcome is where the build happens
        relative to start_wall_clock(), and that is checkable anywhere.
        """
        source = RUNNER.read_text(encoding="utf-8")

        build = source.find("build_font_index_once()\n        start_wall_clock")
        self.assertNotEqual(
            -1,
            build,
            "The font index must be built immediately before the wall clock starts, "
            "or a slow first scan is charged to the learner and killed part-way.",
        )

    def test_a_missing_index_is_created_rather_than_left_half_built(self) -> None:
        cache = self._home / "cache3"
        cache.mkdir()
        workspace = self._home / "workspace3"
        workspace.mkdir()

        completed = run_with_cache(cache, workspace)

        self.assertIn("plotted", completed.stdout, completed.stderr[-1500:])
        self.assertNotEqual(
            [],
            list(cache.glob("fontlist-v*.json")),
            "The index must be on disk afterwards, or the next run rebuilds it again.",
        )

    def test_the_plot_still_reaches_the_ide(self) -> None:
        cache = self._home / "cache2"
        cache.mkdir()
        workspace = self._home / "workspace2"
        workspace.mkdir()

        completed = run_with_cache(cache, workspace)

        self.assertIn(
            "__DATASENSEI_PLOT__" if "__DATASENSEI_PLOT__" in completed.stdout else "PLOT_BASE64",
            completed.stdout,
            "The figure must still be captured and sent back to the IDE.",
        )


if __name__ == "__main__":
    unittest.main()
