"""import os is allowed for data analysis; destructive and process calls are not.

Reported 2026-09-27: "Sandbox policy rejected workspace source:
data-analysis/untitled.py:1: import os is blocked". Learners use os.path,
os.listdir, os.getcwd and os.makedirs in ordinary data-analysis code. Deleting,
renaming or re-permissioning files and running programs stay blocked, whether
the code calls os directly, through an alias, or through pathlib and shutil.
"""
from __future__ import annotations

import os
import shutil
import subprocess
import sys
import tempfile
import textwrap
import unittest
from pathlib import Path


PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"


class OsModulePolicyTest(unittest.TestCase):
    def run_program(self, source: str, extra_files: dict[str, str] | None = None) -> tuple[subprocess.CompletedProcess[str], Path]:
        directory = tempfile.mkdtemp(prefix="datasensei-os-policy-")
        self.addCleanup(shutil.rmtree, directory, True)
        test_root = Path(directory)
        workspace = test_root / "workspace"
        (workspace / "data-analysis").mkdir(parents=True)
        (workspace / "data-analysis" / "sales.csv").write_text("month,amount\nJan,10\nFeb,20\n", encoding="utf-8")
        for name, content in (extra_files or {}).items():
            path = workspace / name
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text(content, encoding="utf-8")

        script = workspace / "data-analysis" / "untitled.py"
        script.write_text(textwrap.dedent(source), encoding="utf-8")
        runner = test_root / "datasensei_runner.py"
        shutil.copy2(RUNNER, runner)

        environment = os.environ.copy()
        environment.update({
            "DS_WORKSPACE": str(workspace),
            "DS_INPUT": str(workspace),
            "DS_CPU_SECONDS": "10",
            "DS_MAX_OUTPUT_BYTES": "200000",
        })

        completed = subprocess.run(
            [sys.executable, "-B", "-u", str(runner), str(script)],
            text=True,
            capture_output=True,
            timeout=60,
            check=False,
            env=environment,
        )

        return completed, workspace

    def test_import_os_for_data_analysis_runs(self) -> None:
        result, workspace = self.run_program("""
            import os
            import csv

            folder = os.path.join(os.getcwd(), "data-analysis")
            print(sorted(name for name in os.listdir(folder) if name.endswith(".csv")))
            with open(os.path.join(folder, "sales.csv"), encoding="utf-8") as handle:
                rows = list(csv.DictReader(handle))
            print(sum(int(row["amount"]) for row in rows))
            os.makedirs("reports", exist_ok=True)
            with open(os.path.join("reports", "total.txt"), "w", encoding="utf-8") as handle:
                handle.write("30")
            print(os.path.exists("reports/total.txt"), os.path.basename(folder))
            for root, dirs, files in os.walk("reports"):
                print(root, files)
        """)

        self.assertEqual(0, result.returncode, result.stderr)
        self.assertIn("['sales.csv']", result.stdout)
        self.assertIn("30", result.stdout)
        self.assertIn("True data-analysis", result.stdout)
        self.assertIn("reports ['total.txt']", result.stdout)
        self.assertNotIn("import os is blocked", result.stderr)

    def test_os_with_pandas_and_matplotlib_still_works(self) -> None:
        try:
            import matplotlib  # noqa: F401
            import pandas  # noqa: F401
        except ImportError:
            self.skipTest("pandas and matplotlib are not installed here")

        result, workspace = self.run_program("""
            import os
            import pandas as pd
            import matplotlib
            matplotlib.use("Agg")
            import matplotlib.pyplot as plt

            frame = pd.read_csv(os.path.join("data-analysis", "sales.csv"))
            print(frame["amount"].sum())
            os.makedirs("charts", exist_ok=True)
            frame.plot(x="month", y="amount")
            plt.savefig(os.path.join("charts", "sales.png"))
            frame.to_csv(os.path.join("charts", "copy.csv"), index=False)
            print(sorted(os.listdir("charts")))
        """)

        self.assertEqual(0, result.returncode, result.stderr[-2000:])
        self.assertIn("30", result.stdout)
        self.assertIn("['copy.csv', 'sales.png']", result.stdout)

    def test_os_remove_and_friends_are_rejected_before_running(self) -> None:
        for source, name in [
            ("import os\nos.remove('data-analysis/sales.csv')\n", "os.remove()"),
            ("import os as o\no.unlink('data-analysis/sales.csv')\n", "os.unlink()"),
            ("from os import remove\nremove('data-analysis/sales.csv')\n", "os.remove()"),
            ("import os\nos.system('echo hi')\n", "os.system()"),
            ("import os\nos.rename('data-analysis/sales.csv', 'x.csv')\n", "os.rename()"),
            ("import os\nos.chmod('data-analysis/sales.csv', 0o777)\n", "os.chmod()"),
        ]:
            with self.subTest(name=name):
                result, workspace = self.run_program(source)
                self.assertNotEqual(0, result.returncode)
                self.assertIn("Sandbox policy rejected workspace source", result.stderr)
                self.assertIn(name + " is blocked", result.stderr)
                self.assertTrue((workspace / "data-analysis" / "sales.csv").exists())

    def test_indirect_access_to_os_is_rejected(self) -> None:
        for source in [
            "import os\ngetattr(os, 'remove')('data-analysis/sales.csv')\n",
            "import os\nvars(os)['remove']('data-analysis/sales.csv')\n",
            "from os import *\n",
        ]:
            with self.subTest(source=source):
                result, _ = self.run_program(source)
                self.assertNotEqual(0, result.returncode)
                self.assertIn("Sandbox policy rejected workspace source", result.stderr)

    def test_deleting_through_pathlib_or_shutil_is_blocked_at_run_time(self) -> None:
        for source in [
            "from pathlib import Path\nPath('data-analysis/sales.csv').unlink()\n",
            "import shutil\nshutil.rmtree('data-analysis')\n",
            "import shutil\nshutil.move('data-analysis/sales.csv', 'moved.csv')\n",
            "from pathlib import Path\nPath('data-analysis/sales.csv').rename('renamed.csv')\n",
        ]:
            with self.subTest(source=source):
                result, workspace = self.run_program(source)
                self.assertNotEqual(0, result.returncode)
                self.assertIn("Sandbox policy blocked this operation", result.stderr)
                self.assertTrue((workspace / "data-analysis" / "sales.csv").exists())

    def test_listing_or_writing_outside_the_workspace_is_blocked(self) -> None:
        # Not under /tmp: the runner's own scratch folder is /tmp, which is
        # private to each container.
        outside = tempfile.mkdtemp(prefix=".datasensei-outside-", dir=Path.home())
        self.addCleanup(shutil.rmtree, outside, True)

        result, _ = self.run_program(f"import os\nprint(os.listdir({outside!r}))\n")
        self.assertNotEqual(0, result.returncode)
        self.assertIn("only your workspace can be listed", result.stderr)

        target = os.path.join(outside, "escape.txt")
        result, _ = self.run_program(
            f"import os\nfd = os.open({target!r}, os.O_WRONLY | os.O_CREAT)\nos.write(fd, b'x')\n"
        )
        self.assertNotEqual(0, result.returncode)
        self.assertIn("Sandbox policy blocked this operation", result.stderr)
        self.assertFalse(os.path.exists(target))

    def test_a_helper_module_in_the_workspace_is_held_to_the_same_rules(self) -> None:
        result, workspace = self.run_program(
            "import helper\nhelper.clean()\n",
            {"data-analysis/helper.py": "import shutil\n\ndef clean():\n    shutil.rmtree('data-analysis')\n"},
        )
        self.assertNotEqual(0, result.returncode)
        self.assertIn("Sandbox policy blocked this operation", result.stderr)
        self.assertTrue((workspace / "data-analysis" / "sales.csv").exists())


if __name__ == "__main__":
    unittest.main()
