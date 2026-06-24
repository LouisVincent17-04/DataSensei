#!/usr/bin/env python3
"""DataSensei educational Python runner.

The Docker container is the primary isolation boundary. This wrapper adds
resource limits, environment scrubbing, an import/audit policy, safe file
access, output limits, and plot capture. The host workspace is mounted
read-only at /input and copied into a size-limited tmpfs at /workspace.
"""

from __future__ import annotations

import builtins
import io
import os
import runpy
import shutil
import sys
from pathlib import Path
from typing import Any


def _env_int(name: str, default: int) -> int:
    try:
        return max(1, int(os.environ.get(name, default)))
    except (TypeError, ValueError):
        return default


INPUT_ROOT = Path(os.environ.get("DS_INPUT", "/input")).resolve()
WORKSPACE = Path(os.environ.get("DS_WORKSPACE", "/workspace")).resolve()
ENTRY = Path(sys.argv[1] if len(sys.argv) > 1 else "/workspace/main.py").resolve()
MAX_OUTPUT = _env_int("DS_MAX_OUTPUT_BYTES", 60000)
MAX_FILE_BYTES = _env_int("DS_MAX_FILE_BYTES", 8 * 1024 * 1024)
MAX_PLOT_BYTES = _env_int("DS_MAX_PLOT_BYTES", 1_500_000)
MAX_PLOTS = _env_int("DS_MAX_PLOTS", 4)
CPU_SECONDS = _env_int("DS_CPU_SECONDS", 8)
MEMORY_BYTES = _env_int("DS_MEMORY_BYTES", 256 * 1024 * 1024)

DENIED_IMPORT_ROOTS = {
    "ctypes", "ensurepip", "fcntl", "ftplib", "http", "importlib",
    "marshal", "multiprocessing", "os", "pickle", "pty", "resource",
    "shelve", "signal", "socket", "subprocess", "telnetlib", "urllib",
    "venv", "webbrowser", "winreg",
}


class LimitedTextWriter(io.TextIOBase):
    def __init__(self, wrapped: io.TextIOBase, limit: int) -> None:
        self.wrapped = wrapped
        self.limit = limit
        self.written = 0
        self.truncated = False

    @property
    def encoding(self) -> str:
        return getattr(self.wrapped, "encoding", "utf-8") or "utf-8"

    def writable(self) -> bool:
        return True

    def write(self, value: str) -> int:
        text = str(value)
        remaining = self.limit - self.written
        if remaining <= 0:
            if not self.truncated:
                self.wrapped.write("\n[Output truncated by DataSensei sandbox]\n")
                self.wrapped.flush()
                self.truncated = True
            return len(text)

        encoded = text.encode("utf-8", errors="replace")
        chunk = encoded[:remaining].decode("utf-8", errors="ignore")
        self.wrapped.write(chunk)
        self.wrapped.flush()
        self.written += len(chunk.encode("utf-8"))

        if len(encoded) > remaining and not self.truncated:
            self.wrapped.write("\n[Output truncated by DataSensei sandbox]\n")
            self.wrapped.flush()
            self.truncated = True

        return len(text)

    def flush(self) -> None:
        self.wrapped.flush()

    def isatty(self) -> bool:
        return False


class SandboxViolation(PermissionError):
    pass


def _inside(path: Path, root: Path) -> bool:
    try:
        path.resolve().relative_to(root.resolve())
        return True
    except (ValueError, OSError, RuntimeError):
        return False


def _prepare_workspace() -> None:
    WORKSPACE.mkdir(parents=True, exist_ok=True)

    if INPUT_ROOT == WORKSPACE:
        return

    if not INPUT_ROOT.is_dir():
        raise SandboxViolation("The private input workspace was not mounted.")

    for source in INPUT_ROOT.rglob("*"):
        if source.is_symlink():
            raise SandboxViolation("Symbolic links are not allowed in the private workspace.")

        relative = source.relative_to(INPUT_ROOT)
        target = WORKSPACE / relative

        if source.is_dir():
            target.mkdir(parents=True, exist_ok=True)
            continue

        if not source.is_file():
            raise SandboxViolation("Unsupported workspace object detected.")

        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(source, target)


def _runtime_read_roots() -> tuple[Path, ...]:
    candidates = {
        Path(sys.base_prefix),
        Path(sys.exec_prefix),
        Path("/usr/lib"),
        Path("/usr/local/lib"),
        Path("/lib"),
        Path("/usr/share/fonts"),
        Path("/usr/share/matplotlib"),
        Path("/opt/datasensei"),
    }
    return tuple(path.resolve() for path in candidates if path.exists())


RUNTIME_READ_ROOTS: tuple[Path, ...] = ()
SPECIAL_READ_FILES = {Path("/proc/cpuinfo"), Path("/proc/meminfo"), Path("/dev/null")}


def _path_from_value(value: object) -> Path | None:
    if isinstance(value, int):
        return None
    if isinstance(value, bytes):
        value = value.decode("utf-8", errors="strict")
    if isinstance(value, (str, os.PathLike)):
        raw = os.fspath(value)
        path = Path(raw).expanduser()
        if not path.is_absolute():
            path = WORKSPACE / path
        return path.resolve()
    return None


def _read_allowed(path: Path) -> bool:
    if _inside(path, WORKSPACE) or _inside(path, Path("/tmp")):
        return True
    if path in SPECIAL_READ_FILES:
        return True
    return any(_inside(path, root) for root in RUNTIME_READ_ROOTS)


def _write_allowed(path: Path) -> bool:
    return _inside(path, WORKSPACE) or _inside(path, Path("/tmp"))


def _mode_is_write(mode: object) -> bool:
    if isinstance(mode, int):
        write_flags = (
            os.O_WRONLY | os.O_RDWR | os.O_CREAT | os.O_TRUNC | os.O_APPEND
        )
        return bool(mode & write_flags)
    return any(flag in str(mode) for flag in ("w", "a", "x", "+"))


def _audit(event: str, args: tuple[object, ...]) -> None:
    blocked_prefixes = (
        "subprocess.", "socket.", "ctypes.dlopen", "os.system", "os.exec",
        "os.spawn", "os.fork", "pty.spawn",
    )
    if event.startswith(blocked_prefixes):
        raise SandboxViolation(f"Operation blocked by DataSensei sandbox policy: {event}")

    if event == "open" and args:
        path = _path_from_value(args[0])
        mode = args[1] if len(args) > 1 else "r"
        if path is not None:
            allowed = _write_allowed(path) if _mode_is_write(mode) else _read_allowed(path)
            if not allowed:
                raise SandboxViolation("File access outside the private workspace is blocked.")

    path_events = {
        "os.remove", "os.unlink", "os.rename", "os.replace", "os.rmdir",
        "os.mkdir", "os.chmod", "os.chown", "os.link", "os.symlink",
        "os.truncate", "os.chdir", "os.listdir", "os.scandir",
    }
    if event in path_events and args:
        for value in args[:2]:
            path = _path_from_value(value)
            if path is not None and not _write_allowed(path):
                raise SandboxViolation("Filesystem operations outside the private workspace are blocked.")


def _apply_resource_limits() -> None:
    try:
        import resource

        resource.setrlimit(resource.RLIMIT_CPU, (CPU_SECONDS, CPU_SECONDS + 1))
        resource.setrlimit(resource.RLIMIT_FSIZE, (MAX_FILE_BYTES, MAX_FILE_BYTES))
        resource.setrlimit(resource.RLIMIT_NOFILE, (64, 64))
        resource.setrlimit(resource.RLIMIT_CORE, (0, 0))

        if hasattr(resource, "RLIMIT_NPROC"):
            resource.setrlimit(resource.RLIMIT_NPROC, (32, 32))

        if os.environ.get("DS_USE_RLIMIT_AS") == "1" and hasattr(resource, "RLIMIT_AS"):
            resource.setrlimit(resource.RLIMIT_AS, (MEMORY_BYTES, MEMORY_BYTES))
    except Exception:
        # Docker cgroup and tmpfs limits remain active if a platform lacks rlimit.
        pass


def _scrub_environment() -> None:
    safe = {
        "HOME": "/tmp",
        "TMPDIR": "/tmp",
        "MPLBACKEND": "Agg",
        "MPLCONFIGDIR": "/tmp/matplotlib",
        "PYTHONIOENCODING": "utf-8",
        "PYTHONDONTWRITEBYTECODE": "1",
        "LANG": "C.UTF-8",
        "LC_ALL": "C.UTF-8",
    }
    os.environ.clear()
    os.environ.update(safe)
    Path("/tmp/matplotlib").mkdir(parents=True, exist_ok=True)


def _install_safe_file_api() -> None:
    original_open = builtins.open
    original_io_open = io.open
    original_os_open = os.open

    def safe_open(file: Any, mode: str = "r", *args: Any, **kwargs: Any):
        path = _path_from_value(file)
        if path is None:
            return original_open(file, mode, *args, **kwargs)
        allowed = _write_allowed(path) if _mode_is_write(mode) else _read_allowed(path)
        if not allowed:
            raise SandboxViolation("File access outside the private workspace is blocked.")
        return original_open(path, mode, *args, **kwargs)

    def safe_io_open(file: Any, mode: str = "r", *args: Any, **kwargs: Any):
        path = _path_from_value(file)
        if path is None:
            return original_io_open(file, mode, *args, **kwargs)
        allowed = _write_allowed(path) if _mode_is_write(mode) else _read_allowed(path)
        if not allowed:
            raise SandboxViolation("File access outside the private workspace is blocked.")
        return original_io_open(path, mode, *args, **kwargs)

    def safe_os_open(path_value: Any, flags: int, mode: int = 0o777, *, dir_fd: int | None = None):
        if dir_fd is not None:
            raise SandboxViolation("Directory file descriptors are not allowed in the learning sandbox.")
        path = _path_from_value(path_value)
        if path is None:
            raise SandboxViolation("Invalid file path.")
        allowed = _write_allowed(path) if _mode_is_write(flags) else _read_allowed(path)
        if not allowed:
            raise SandboxViolation("File access outside the private workspace is blocked.")
        return original_os_open(path, flags, mode)

    builtins.open = safe_open
    io.open = safe_io_open
    os.open = safe_os_open


def _disable_process_helpers() -> None:
    def blocked(*_args: object, **_kwargs: object) -> None:
        raise SandboxViolation("Operating-system process execution is blocked.")

    for name in (
        "system", "popen", "fork", "forkpty", "kill", "killpg",
        "execl", "execle", "execlp", "execlpe", "execv", "execve",
        "execvp", "execvpe", "spawnl", "spawnle", "spawnlp", "spawnlpe",
        "spawnv", "spawnve", "spawnvp", "spawnvpe",
    ):
        if hasattr(os, name):
            setattr(os, name, blocked)


def _patch_matplotlib_show() -> None:
    plt = sys.modules.get("matplotlib.pyplot")
    if plt is None or getattr(plt, "_datasensei_patched", False):
        return

    import base64

    def safe_show(*_args: object, **_kwargs: object) -> None:
        emitted = 0
        for figure_number in plt.get_fignums():
            if emitted >= MAX_PLOTS:
                print("[Additional plot omitted by sandbox limit]")
                break

            figure = plt.figure(figure_number)
            figure.set_size_inches(
                min(figure.get_figwidth(), 12),
                min(figure.get_figheight(), 8),
            )
            buffer = io.BytesIO()
            figure.savefig(buffer, format="png", bbox_inches="tight", dpi=90)
            payload = buffer.getvalue()
            if len(payload) > MAX_PLOT_BYTES:
                print("[Oversized plot omitted by sandbox limit]")
                continue

            encoded = base64.b64encode(payload).decode("ascii")
            print(f"__PLOT_BASE64__:{encoded}:__END_PLOT__")
            emitted += 1

        plt.close("all")

    plt.show = safe_show
    plt._datasensei_patched = True


def _student_import_caller(globals_dict: object) -> bool:
    if not isinstance(globals_dict, dict):
        return False

    caller_file = globals_dict.get("__file__")
    if not caller_file:
        return False

    try:
        return _inside(Path(str(caller_file)).resolve(), WORKSPACE)
    except (OSError, RuntimeError, ValueError):
        return False


def _install_import_hook() -> None:
    original_import = builtins.__import__

    def safe_import(name: str, globals=None, locals=None, fromlist=(), level=0):
        root = name.split(".", 1)[0]

        # Block dangerous modules only when the import originates from student
        # workspace code. Trusted packages such as pandas and matplotlib may
        # legitimately import os internally, so blocking all callers would break
        # normal data-science programs.
        if root in DENIED_IMPORT_ROOTS and _student_import_caller(globals):
            raise SandboxViolation(
                f"Import of '{root}' is blocked by the DataSensei sandbox policy."
            )

        module = original_import(name, globals, locals, fromlist, level)
        if name == "matplotlib" or name.startswith("matplotlib."):
            _patch_matplotlib_show()
        return module

    builtins.__import__ = safe_import


def main() -> int:
    global RUNTIME_READ_ROOTS

    try:
        _prepare_workspace()
    except (SandboxViolation, OSError) as exc:
        print(f"Workspace preparation failed: {exc}", file=sys.stderr)
        return 126

    if not _inside(ENTRY, WORKSPACE):
        print("Entry file must be inside the private workspace.", file=sys.stderr)
        return 126

    if not ENTRY.is_file():
        print("Python entry file was not found.", file=sys.stderr)
        return 2

    _apply_resource_limits()
    _scrub_environment()
    RUNTIME_READ_ROOTS = _runtime_read_roots()
    _disable_process_helpers()
    sys.addaudithook(_audit)
    _install_import_hook()
    _install_safe_file_api()

    sys.stdout = LimitedTextWriter(sys.__stdout__, MAX_OUTPUT)
    sys.stderr = LimitedTextWriter(sys.__stderr__, MAX_OUTPUT)
    sys.setrecursionlimit(min(sys.getrecursionlimit(), 2000))

    os.chdir(WORKSPACE)
    sys.argv = [str(ENTRY)]

    try:
        runpy.run_path(str(ENTRY), run_name="__main__")
        return 0
    except SandboxViolation as exc:
        print(f"Sandbox policy blocked this operation: {exc}", file=sys.stderr)
        return 126
    except MemoryError:
        print("Execution stopped because the sandbox memory limit was exceeded.", file=sys.stderr)
        return 137
    except SystemExit as exc:
        if exc.code is None:
            return 0
        if isinstance(exc.code, int):
            return exc.code
        print(str(exc.code), file=sys.stderr)
        return 1
    except BaseException:
        import traceback

        traceback.print_exc()
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
