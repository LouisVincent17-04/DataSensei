"""The machine's temp folder must be usable by installed library code.

SciPy's MessageStream opens a temp file while scipy.stats is imported, and
scikit-learn imports scipy.stats. The sandbox hard-coded /tmp as the writable
temp root, which is right inside the container and wrong everywhere else: on
Windows the temp folder is the user's own AppData\\Local\\Temp, so the open was
refused and every scikit-learn lesson died with

    Sandbox policy blocked this operation: open (C:\\Users\\...\\AppData\\Local\\Temp\\7luwjtw3)

The allowance is for library code only. A learner writing to the temp folder
is still refused, which is what the second half of this test pins down.

Run with:  python -m unittest tests/Python/test_sandbox_temp_directory.py
"""

from __future__ import annotations

import json
import os
import subprocess
import sys
import tempfile
import textwrap
import unittest
from pathlib import Path

PROJECT_ROOT = Path(__file__).resolve().parents[2]
RUNNER = PROJECT_ROOT / "docker/python-runner/datasensei_runner.py"

# The probe writes a throwaway module into a real site-packages directory so
# that one of the calls genuinely comes from installed library code. Trust is
# decided by the call stack, so it cannot be faked from the test file itself.
PROBE = textwrap.dedent(
    '''
    import importlib.util, json, os, site, sys, tempfile
    from pathlib import Path

    runner_path, workspace = sys.argv[1], sys.argv[2]

    # Written BEFORE the runner is imported: importing it installs the audit
    # hook, and site-packages is a read root, not a write root, so the helper
    # could not be created afterwards.
    helper = None
    for directory in site.getsitepackages():
        candidate = Path(directory) / "ds_trusted_probe.py"
        try:
            candidate.write_text(
                "def ask(runner, path, mode):\\n"
                "    return runner.file_access_is_allowed(path, mode)\\n",
                encoding="utf-8",
            )
        except Exception:
            continue
        helper = candidate
        break

    spec = importlib.util.spec_from_file_location("ds_runner_under_test", runner_path)
    runner = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(runner)

    temp_file = str(Path(tempfile.gettempdir()) / "ds_probe_file")

    temp_root = runner.PLATFORM_TEMP_DIR
    inside_existing_roots = temp_root is not None and any(
        temp_root == root or root in temp_root.parents
        for root in runner.ALLOWED_WRITE_ROOTS
    )

    answers = {
        "temp_dir_detected": temp_root is not None,
        "temp_dir": str(temp_root),
        "temp_dir_inside_roots": inside_existing_roots,
        "untrusted_write": runner.file_access_is_allowed(temp_file, "w"),
        "untrusted_read": runner.file_access_is_allowed(temp_file, "r"),
        "outside_write": runner.file_access_is_allowed(str(Path.home() / "escape"), "w"),
        "workspace_write": runner.file_access_is_allowed(workspace + "/out.txt", "w"),
    }

    # Now the same question asked from inside site-packages.
    if helper is None:
        answers["trusted_write"] = None
    else:
        try:
            sys.path.insert(0, str(helper.parent))
            import ds_trusted_probe
            answers["trusted_write"] = ds_trusted_probe.ask(runner, temp_file, "w")
            answers["trusted_escape"] = ds_trusted_probe.ask(runner, str(Path.home() / "escape"), "w")
        finally:
            try:
                helper.unlink()
            except Exception:
                pass

    sys.stdout.write("__PROBE__" + json.dumps(answers))
    '''
)


class SandboxTempDirectory(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls._directory = tempfile.TemporaryDirectory()
        workspace = Path(cls._directory.name) / "workspace"
        workspace.mkdir(parents=True, exist_ok=True)

        probe = Path(cls._directory.name) / "probe.py"
        probe.write_text(PROBE, encoding="utf-8")

        # A temp folder that is NOT /tmp and not inside any existing root, so
        # this runs the Windows case rather than the container one. On Linux
        # /tmp is a writable root already and the allowance never applies.
        cls._fake_temp = PROJECT_ROOT / "storage/framework/testing" / ("ds-temp-" + str(os.getpid()))
        cls._fake_temp.mkdir(parents=True, exist_ok=True)

        environment = os.environ.copy()
        environment.update({
            "DS_WORKSPACE": str(workspace),
            "DS_INPUT": str(Path(cls._directory.name) / "input"),
            "TMPDIR": str(cls._fake_temp),
            "TMP": str(cls._fake_temp),
            "TEMP": str(cls._fake_temp),
        })

        completed = subprocess.run(
            [sys.executable, "-B", str(probe), str(RUNNER), str(workspace)],
            capture_output=True, text=True, timeout=120, env=environment,
        )

        marker = completed.stdout.find("__PROBE__")
        if marker == -1:
            raise AssertionError(
                "The temp-directory probe produced no result.\n"
                f"stdout: {completed.stdout}\nstderr: {completed.stderr}"
            )

        cls.answers = json.loads(completed.stdout[marker + len("__PROBE__"):])

    @classmethod
    def tearDownClass(cls) -> None:
        cls._directory.cleanup()

        import shutil

        shutil.rmtree(cls._fake_temp, ignore_errors=True)

    def test_the_machines_temp_folder_is_known(self) -> None:
        self.assertTrue(
            self.answers["temp_dir_detected"],
            "Without a temp folder the allowance cannot apply anywhere.",
        )
        self.assertFalse(
            self.answers["temp_dir_inside_roots"],
            "This test must run the case where the temp folder is outside the roots.",
        )

    def test_library_code_may_use_the_temp_folder(self) -> None:
        if self.answers.get("trusted_write") is None:
            self.skipTest("No writable site-packages directory to host the probe module.")

        self.assertTrue(
            self.answers["trusted_write"],
            "SciPy cannot import if library code cannot open its temp file.",
        )

    def test_learner_code_still_cannot_use_the_temp_folder(self) -> None:
        self.assertFalse(
            self.answers["untrusted_write"],
            "A learner's own temp write must still be refused.",
        )

    def test_the_allowance_does_not_reach_outside_the_temp_folder(self) -> None:
        self.assertFalse(
            self.answers["outside_write"],
            "Only the temp folder is allowed, not the rest of the filesystem.",
        )

        if self.answers.get("trusted_escape") is not None:
            self.assertFalse(
                self.answers["trusted_escape"],
                "Even library code stays inside the temp folder and the roots.",
            )

    def test_the_workspace_is_unaffected(self) -> None:
        self.assertTrue(self.answers["workspace_write"])


if __name__ == "__main__":
    unittest.main()
