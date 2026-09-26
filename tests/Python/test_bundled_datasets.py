"""Lesson datasets must load without touching the network.

sns.load_dataset() downloads on first use and containers run with
--network none, so every visualisation lesson that called it failed inside
the sandbox. The example CSVs now ship beside the runner and SEABORN_DATA
points at them; seaborn only downloads when the cached file is missing.

Run with:  python -m unittest tests/Python/test_bundled_datasets.py
"""

from __future__ import annotations

import subprocess
import sys
import tempfile
import textwrap
import unittest
from pathlib import Path

PROJECT_ROOT = Path(__file__).resolve().parents[2]
DATASETS = PROJECT_ROOT / "docker/python-runner/seaborn-data"
DOCKERFILE = PROJECT_ROOT / "docker/python-runner/Dockerfile"

# Every dataset the module lessons ask for by name.
REQUIRED = ["iris", "titanic", "tips", "penguins", "flights"]


class BundledDatasets(unittest.TestCase):
    def test_every_dataset_a_lesson_uses_is_present(self) -> None:
        for name in REQUIRED:
            path = DATASETS / f"{name}.csv"
            self.assertTrue(path.is_file(), f"{name}.csv is missing from the bundle.")
            self.assertGreater(path.stat().st_size, 200, f"{name}.csv looks empty.")

    def test_the_image_ships_them_and_points_seaborn_at_them(self) -> None:
        dockerfile = DOCKERFILE.read_text(encoding="utf-8")

        self.assertIn(
            "COPY seaborn-data /opt/datasensei/seaborn-data",
            dockerfile,
            "The image must carry the datasets; the container has no network to fetch them.",
        )
        self.assertIn(
            "ENV SEABORN_DATA=/opt/datasensei/seaborn-data",
            dockerfile,
            "seaborn looks in SEABORN_DATA; without it the cache is not found.",
        )

    def test_seaborn_loads_them_with_the_downloader_disabled(self) -> None:
        try:
            import seaborn  # noqa: F401
        except ModuleNotFoundError:
            self.skipTest("seaborn is not installed on this machine.")

        # The downloader is replaced with something that raises, so any attempt
        # to reach the network fails the test rather than quietly succeeding on
        # a machine that happens to be online.
        script = textwrap.dedent(
            """
            import sys
            import seaborn.utils as utils

            def refuse(url, path):
                raise AssertionError("network access attempted: " + url)

            utils.urlretrieve = refuse

            import seaborn as sns

            for name in %r:
                frame = sns.load_dataset(name)
                assert len(frame) > 0, name

            sys.stdout.write("OK")
            """
        ) % (REQUIRED,)

        with tempfile.TemporaryDirectory() as directory:
            probe = Path(directory) / "probe.py"
            probe.write_text(script, encoding="utf-8")

            completed = subprocess.run(
                [sys.executable, "-B", str(probe)],
                capture_output=True, text=True, timeout=120,
                env={"SEABORN_DATA": str(DATASETS), "PATH": "/usr/bin:/bin", "HOME": directory},
            )

        self.assertIn(
            "OK",
            completed.stdout,
            "Datasets did not load offline.\n" + completed.stderr[-2000:],
        )

    def test_an_empty_cache_would_need_the_network(self) -> None:
        """The counterpart: without the bundle, seaborn does reach out.

        Without this, the test above would still pass on a machine whose
        cache happened to be populated some other way, and would prove nothing.
        """
        try:
            import seaborn  # noqa: F401
        except ModuleNotFoundError:
            self.skipTest("seaborn is not installed on this machine.")

        script = textwrap.dedent(
            """
            import sys
            import seaborn.utils as utils

            def refuse(url, path):
                raise AssertionError("would download")

            utils.urlretrieve = refuse

            import seaborn as sns

            try:
                sns.load_dataset("tips")
                sys.stdout.write("NO_DOWNLOAD")
            except AssertionError:
                sys.stdout.write("DOWNLOAD_ATTEMPTED")
            """
        )

        with tempfile.TemporaryDirectory() as directory:
            probe = Path(directory) / "probe.py"
            probe.write_text(script, encoding="utf-8")
            empty_cache = Path(directory) / "empty"
            empty_cache.mkdir()

            completed = subprocess.run(
                [sys.executable, "-B", str(probe)],
                capture_output=True, text=True, timeout=120,
                env={"SEABORN_DATA": str(empty_cache), "PATH": "/usr/bin:/bin", "HOME": directory},
            )

        self.assertIn("DOWNLOAD_ATTEMPTED", completed.stdout, completed.stderr[-2000:])


if __name__ == "__main__":
    unittest.main()
