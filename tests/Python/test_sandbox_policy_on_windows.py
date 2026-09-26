"""Two sandbox-policy faults that only appear on the unsafe local driver.

Both were found running the IDE on Windows while Docker was down.

1. "import matplotlib" died with

       Sandbox policy blocked this operation: ctypes.dlopen (kernel32)

   CPython's own ctypes module binds kernel32 as it is imported, and numpy
   imports ctypes, so numpy, pandas, matplotlib, seaborn and scikit-learn were
   all unreachable. The dlopen was already coming from trusted library code;
   only the library name was unrecognised.

2. Printing the resulting traceback died with

       Sandbox policy blocked this operation: open (...datasensei_runner.py)

   Python reads the source of every frame it prints. In the container the
   runner lives under /opt/datasensei, which is a read root; run from the
   project tree it matched none, so the error report itself raised and buried
   the learner's actual mistake.

The container path must keep refusing everything it refused before, so the
negative cases are asserted just as hard as the positive ones.

The probe runs in a child process on purpose: importing the runner installs a
sys.addaudithook, which cannot be removed, and it would police the test runner
itself for the rest of the session.

Run with:  python -m unittest tests/Python/test_sandbox_policy_on_windows.py
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

PROBE = textwrap.dedent(
    """
    import importlib.util, json, site, sys
    from pathlib import Path

    # Written before the runner is imported: the audit hook makes
    # site-packages read-only once it is installed.
    helper = None
    for directory in site.getsitepackages():
        candidate = Path(directory) / "ds_dlopen_probe.py"
        try:
            candidate.write_text(
                "def ask(runner, name):\\n"
                "    return runner.ctypes_dlopen_is_allowed((name,))\\n",
                encoding="utf-8",
            )
        except Exception:
            continue
        helper = candidate
        break

    spec = importlib.util.spec_from_file_location("ds_runner_under_test", sys.argv[1])
    runner = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(runner)

    workspace = sys.argv[2]
    keywords = [k.lower() for k in runner.TRUSTED_DLOPEN_LIBRARY_KEYWORDS]

    def known(library):
        return any(keyword in library for keyword in keywords)

    answers = {
        "runner_readable": runner.file_access_is_allowed(sys.argv[1], "r"),
        "runner_writable": runner.file_access_is_allowed(sys.argv[1], "w"),
        "workspace_readable": runner.file_access_is_allowed(workspace + "/main.py", "r"),
        "workspace_writable": runner.file_access_is_allowed(workspace + "/main.py", "w"),
        "crt_known": {n: known(n) for n in ["kernel32", "msvcrt", "ucrtbase", "oleaut32"]},
        # The Windows rule is by shape, not by name: a bare DLL name or one
        # inside the system folder, and only from installed library code.
        "bare_names": {
            n: runner.windows_system_library(n, n.lower())
            for n in ["user32", "kernel32.dll", "advapi32", "gdi32"]
        },
        "outside_system_folder": {
            n: runner.windows_system_library(n, n.lower().replace("\\\\", "/"))
            for n in ["C:/Users/someone/Downloads/evil.dll", "D:/payload.dll"]
        },
        # This probe is not library code, so the stack here is untrusted:
        # exactly the position a learner's own program is in.
        "untrusted_dlopen": {
            n: runner.ctypes_dlopen_is_allowed((n,))
            for n in ["kernel32", "msvcrt", "libc.so.6", "numpy",
                      "user32", "ws2_32", "wininet", "C:/Users/someone/evil.dll"]
        },
        "outside_readable": {
            p: runner.file_access_is_allowed(p, "r")
            for p in ["/etc/passwd", "/etc/shadow", "/root/.ssh/id_rsa"]
        },
    }

    # The same question asked from installed library code, with the runner
    # told it is on Windows so the platform branch actually runs here.
    if helper is None:
        answers["trusted_dlopen"] = None
    else:
        try:
            sys.path.insert(0, str(helper.parent))
            import ds_dlopen_probe

            runner.ON_WINDOWS = True
            answers["trusted_dlopen"] = {
                n: ds_dlopen_probe.ask(runner, n)
                for n in ["user32", "gdi32", "C:/Users/someone/Downloads/evil.dll"]
            }
        finally:
            runner.ON_WINDOWS = False
            try:
                helper.unlink()
            except Exception:
                pass

    sys.stdout.write("__PROBE__" + json.dumps(answers))
    """
)


class SandboxPolicyOnWindows(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls._directory = tempfile.TemporaryDirectory()
        workspace = Path(cls._directory.name) / "workspace"
        workspace.mkdir(parents=True, exist_ok=True)

        probe = Path(cls._directory.name) / "probe.py"
        probe.write_text(PROBE, encoding="utf-8")

        environment = os.environ.copy()
        environment.update({
            "DS_WORKSPACE": str(workspace),
            "DS_INPUT": str(Path(cls._directory.name) / "input"),
        })

        completed = subprocess.run(
            [sys.executable, "-B", str(probe), str(RUNNER), str(workspace)],
            capture_output=True,
            text=True,
            timeout=120,
            env=environment,
        )

        marker = completed.stdout.find("__PROBE__")
        if marker == -1:
            raise AssertionError(
                "The policy probe produced no result.\n"
                f"stdout: {completed.stdout}\nstderr: {completed.stderr}"
            )

        cls.answers = json.loads(completed.stdout[marker + len("__PROBE__"):])

    @classmethod
    def tearDownClass(cls) -> None:
        cls._directory.cleanup()

    def test_the_runner_source_can_be_read_for_tracebacks(self) -> None:
        self.assertTrue(
            self.answers["runner_readable"],
            "Traceback printing must be able to read the runner's own source.",
        )

    def test_the_runner_source_still_cannot_be_written(self) -> None:
        self.assertFalse(
            self.answers["runner_writable"],
            "The runner folder is readable, never writable.",
        )

    def test_the_c_runtime_libraries_windows_needs_are_recognised(self) -> None:
        for library, recognised in self.answers["crt_known"].items():
            self.assertTrue(
                recognised,
                f"{library} must be loadable or numpy cannot import on Windows.",
            )

    def test_installed_libraries_may_bind_windows_system_dlls(self) -> None:
        # dateutil opens user32 for time zone names and pandas imports
        # dateutil, so "import pandas" failed on a name-by-name allowlist.
        for library, allowed in self.answers["bare_names"].items():
            self.assertTrue(allowed, f"Library code must be able to bind {library}.")

    def test_library_code_on_windows_reaches_the_system_dll_rule(self) -> None:
        answers = self.answers.get("trusted_dlopen")

        if answers is None:
            self.skipTest("No writable site-packages directory to host the probe module.")

        self.assertTrue(answers["user32"], '"import pandas" needs dateutil to bind user32.')
        self.assertTrue(answers["gdi32"])
        self.assertFalse(
            answers["C:/Users/someone/Downloads/evil.dll"],
            "Trusted code still cannot load an arbitrary DLL off disk.",
        )

    def test_a_dll_outside_the_system_folder_is_refused(self) -> None:
        # The widening is deliberately shaped: a bare system name or the
        # Windows system folder, never an arbitrary file on disk.
        for library, allowed in self.answers["outside_system_folder"].items():
            self.assertFalse(allowed, f"{library} is not a system library and must be refused.")

    def test_a_dlopen_from_untrusted_code_is_refused_whatever_the_library(self) -> None:
        for library, allowed in self.answers["untrusted_dlopen"].items():
            self.assertFalse(
                allowed,
                f"A learner reaching ctypes.dlopen({library}) must still be refused.",
            )

    def test_paths_outside_every_root_are_still_refused(self) -> None:
        for path, allowed in self.answers["outside_readable"].items():
            self.assertFalse(allowed, f"{path} must stay unreadable.")

    def test_the_workspace_is_still_readable_and_writable(self) -> None:
        self.assertTrue(self.answers["workspace_readable"])
        self.assertTrue(self.answers["workspace_writable"])


if __name__ == "__main__":
    unittest.main()
