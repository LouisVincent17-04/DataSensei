"""The runner must start where the POSIX "resource" module does not exist.

The unsafe local driver runs datasensei_runner.py directly on the host. On
Windows there is no "resource" module, so the top-level "import resource"
killed every run before the learner's code was reached:

    ModuleNotFoundError: No module named 'resource'

The import is optional now and the rlimits are skipped when it is absent. The
container path is unaffected and still applies every limit.

Run with:  python -m unittest tests/Python/test_runner_without_posix_resource.py
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

# Stands in for Windows: "resource" is refused before it can be imported, on
# this interpreter only, so the test works on a POSIX machine.
BLOCK_RESOURCE = textwrap.dedent(
    """
    import sys
    from importlib.abc import MetaPathFinder

    class _NoResourceModule(MetaPathFinder):
        def find_spec(self, name, path=None, target=None):
            if name == "resource":
                raise ModuleNotFoundError("No module named 'resource'")
            return None

    sys.meta_path.insert(0, _NoResourceModule())
    """
)


class RunnerWithoutPosixResource(unittest.TestCase):
    def test_a_program_runs_when_the_resource_module_is_missing(self) -> None:
        with tempfile.TemporaryDirectory() as root_directory:
            root = Path(root_directory)
            workspace = root / "workspace"
            workspace.mkdir()
            (workspace / "main.py").write_text(
                "print('hello from the local driver')\nprint('2+2 =', 2 + 2)\n",
                encoding="utf-8",
            )

            # The runner lives beside the workspace exactly as the local
            # driver invokes it, so the sandbox's trusted-path check passes.
            runner = root / "datasensei_runner.py"
            shutil.copy2(RUNNER, runner)

            bootstrap = root / "bootstrap.py"
            bootstrap.write_text(
                BLOCK_RESOURCE
                + textwrap.dedent(
                    f"""
                    import sys
                    runner = {str(runner)!r}
                    sys.argv = [runner, {str(workspace / "main.py")!r}]
                    source = open(runner, encoding="utf-8").read()
                    exec(
                        compile(source, runner, "exec"),
                        {{"__name__": "__main__", "__file__": runner}},
                    )
                    """
                ),
                encoding="utf-8",
            )

            environment = os.environ.copy()
            environment.update({
                "DS_WORKSPACE": str(workspace),
                "DS_INPUT": str(root / "no-input"),
                "DS_CPU_SECONDS": "120",
                "DS_WALL_SECONDS": "60",
                "DS_MAX_OUTPUT_BYTES": "200000",
            })

            completed = subprocess.run(
                [sys.executable, "-B", str(bootstrap)],
                cwd=str(workspace),
                capture_output=True,
                text=True,
                timeout=120,
                env=environment,
            )

        self.assertNotIn(
            "No module named 'resource'",
            completed.stdout + completed.stderr,
            "The runner must not abort when the POSIX resource module is absent.",
        )
        self.assertIn("hello from the local driver", completed.stdout)
        self.assertIn("2+2 = 4", completed.stdout)
        self.assertEqual(0, completed.returncode, completed.stderr)

    def test_the_limits_are_still_applied_where_resource_exists(self) -> None:
        source = RUNNER.read_text(encoding="utf-8")

        self.assertIn(
            "resource.setrlimit(resource.RLIMIT_CPU",
            source,
            "The container path must keep its CPU limit.",
        )
        self.assertIn(
            "resource.setrlimit(resource.RLIMIT_FSIZE",
            source,
            "The container path must keep its file-size limit.",
        )
        self.assertIn(
            "if resource is None:",
            source,
            "The limits must be skipped rather than crashing when resource is absent.",
        )


if __name__ == "__main__":
    unittest.main()
