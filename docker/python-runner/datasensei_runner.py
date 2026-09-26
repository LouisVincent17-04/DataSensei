#!/usr/bin/env python3

# ------------------------------------------------------------
# Attach helper (warm sandbox, protocol 4)
# ------------------------------------------------------------
# Laravel talks to a standby container with exactly ONE "docker exec" per
# step: "start" hands over the job, "feed" hands over the next input() answer.
# Either way this helper then waits inside the container until the program
# asks for more input or ends, and prints one JSON document. It runs before the
# heavy imports below on purpose: it must cost milliseconds, not the start-up
# of the whole runner.
import os
import sys

if len(sys.argv) > 1 and sys.argv[1] == "--attach":
    import json
    import time

    _dir = os.environ.get("DS_SESSION_DIR", "/tmp")
    _p = lambda name: os.path.join(_dir, name)

    def _read_state():
        try:
            with open(_p(".ds_state"), "r", encoding="utf-8") as handle:
                return json.load(handle)
        except Exception:
            return None

    def _read_text(name, limit):
        try:
            with open(_p(name), "rb") as handle:
                return handle.read(limit).decode("utf-8", errors="replace")
        except OSError:
            return ""

    def _reply(payload):
        sys.stdout.write(json.dumps(payload))
        sys.stdout.flush()
        raise SystemExit(0)

    try:
        request = json.loads(sys.stdin.read() or "{}")
    except Exception:
        _reply({"state": "bad_request"})

    limit = int(request.get("max_bytes") or 16 * 1024 * 1024)
    wait_seconds = float(request.get("wait") or 14)
    pickup_seconds = float(request.get("pickup") or 6)
    seen_seq = int(request.get("seq") or 0)

    if request.get("op") == "start":
        try:
            os.close(os.open(_p(".ds_claim"), os.O_CREAT | os.O_EXCL | os.O_WRONLY, 0o600))
        except FileExistsError:
            _reply({"state": "taken"})
        except OSError:
            _reply({"state": "unavailable"})

        with open(_p(".ds_job.tmp"), "w", encoding="utf-8") as handle:
            json.dump(request.get("job") or {}, handle)
        os.rename(_p(".ds_job.tmp"), _p(".ds_job"))
    elif request.get("op") == "feed":
        with open(_p(".ds_stdin"), "ab") as handle:
            handle.write(str(request.get("lines") or "").encode("utf-8"))
    else:
        _reply({"state": "bad_request"})

    began = time.monotonic()
    started_at = None if request.get("op") == "start" else began

    while True:
        state = _read_state()
        name = (state or {}).get("state")
        now = time.monotonic()

        if started_at is None and name in ("started", "waiting", "exited"):
            # The execution clock starts when the program starts, not while
            # the container is still getting ready.
            started_at = now

        if state and name in ("waiting", "exited") and int(state.get("seq") or 0) > seen_seq:
            payload = dict(state)
            payload["stdout"] = _read_text(".ds_out", limit)
            payload["stderr"] = _read_text(".ds_err", limit)
            if name == "exited":
                # Lets the runner end (and the container stop) only now that
                # everything has been read.
                try:
                    open(_p(".ds_ack"), "w").close()
                except OSError:
                    pass
            _reply(payload)

        if started_at is None and now - began > pickup_seconds:
            _reply({"state": "not_started"})

        if started_at is not None and now - started_at > wait_seconds:
            _reply({"state": "timeout", "stdout": _read_text(".ds_out", limit), "stderr": _read_text(".ds_err", limit)})

        time.sleep(0.004)


import ast
import base64
import builtins
import importlib.abc
import importlib.util
import inspect
import io
import json
import runpy
import shutil
import site
import time
import threading
import traceback
from pathlib import Path

try:
    import resource
except ModuleNotFoundError:
    # POSIX only. The container is the real boundary and always has it; the
    # unsafe local driver on Windows does not, and refusing to start there
    # would leave the IDE dead with an import traceback. The limits below are
    # simply skipped, which is one more reason local mode is for a developer
    # machine and never for a shared server.
    resource = None


RUNNER_FILE = Path(__file__).resolve(strict=False)
WORKSPACE = Path(os.environ.get("DS_WORKSPACE", "/workspace")).resolve(strict=False)
INPUT_DIR = Path(os.environ.get("DS_INPUT", "/input")).resolve(strict=False)
OUTPUT_DIR = WORKSPACE / "datasensei_outputs"
INPUT_REQUIRED_MARKER = "__DATASENSEI_INPUT_REQUIRED__:"
INPUTS_CONSUMED_MARKER = "__DATASENSEI_INPUTS_CONSUMED__:"

# Protocol 2 (warm sandbox): the container is started ahead of time with
# "--standby", has no host mount at all, and receives its workspace, stdin and
# limits as one JSON job file. While a program waits in input() the container
# stays alive and later answers are appended to SESSION_STDIN, so the program
# continues instead of being replayed from the start for every answer.
RUNNER_PROTOCOL = 4
STANDBY_MODE = len(sys.argv) > 1 and sys.argv[1] == "--standby"
SESSION_DIR = Path(os.environ.get("DS_SESSION_DIR", "/tmp"))
SESSION_JOB = SESSION_DIR / ".ds_job"
SESSION_STDIN = SESSION_DIR / ".ds_stdin"
SESSION_STATE = SESSION_DIR / ".ds_state"
SESSION_OUT = SESSION_DIR / ".ds_out"
SESSION_ERR = SESSION_DIR / ".ds_err"
SESSION_ACK = SESSION_DIR / ".ds_ack"

os.environ.setdefault("MPLBACKEND", "Agg")
os.environ.setdefault("MPLCONFIGDIR", "/tmp/matplotlib")
os.environ.setdefault("XDG_CACHE_HOME", "/tmp/.cache")
os.environ.setdefault("PYTHONNOUSERSITE", "1")
# The sandbox has no shared memory (--ipc none), so joblib cannot use processes
# anyway. Saying so up front keeps its "will operate in serial mode" warning
# out of every scikit-learn lesson's output.
os.environ.setdefault("JOBLIB_MULTIPROCESSING", "0")
os.environ.setdefault("LOKY_MAX_CPU_COUNT", "1")

Path("/tmp/matplotlib").mkdir(parents=True, exist_ok=True)
Path("/tmp/.cache").mkdir(parents=True, exist_ok=True)
WORKSPACE.mkdir(parents=True, exist_ok=True)


# ------------------------------------------------------------
# Prepare workspace
# ------------------------------------------------------------
def copy_input_workspace_to_runtime_workspace() -> None:
    """
    Laravel mounts the real saved IDE files at /input as read-only.
    Docker mounts /workspace as a fresh tmpfs for every run.

    Therefore, before executing the student file, we must copy /input into
    /workspace. Without this, /workspace is empty and the runner throws:
    Python file not found: /workspace/<file>.py
    """
    if not INPUT_DIR.exists() or not INPUT_DIR.is_dir():
        return

    if INPUT_DIR == WORKSPACE:
        return

    for source in INPUT_DIR.rglob("*"):
        relative = source.relative_to(INPUT_DIR)
        target = WORKSPACE / relative

        if source.is_dir():
            target.mkdir(parents=True, exist_ok=True)
            continue

        if source.is_file():
            target.parent.mkdir(parents=True, exist_ok=True)
            try:
                if target.exists() and source.samefile(target):
                    continue
            except Exception:
                pass
            shutil.copy2(source, target)
            try:
                target.chmod(0o644)
            except Exception:
                pass


copy_input_workspace_to_runtime_workspace()
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)


# ------------------------------------------------------------
# Matplotlib setup
# ------------------------------------------------------------
# Importing matplotlib.pyplot costs most of a second of CPU (several seconds in
# a half-CPU container with a cold font cache), and the old runner paid that on
# every run, including print("hello") and every replay for input(). It is now
# configured only when the program (or a module it imports) really imports it.
# MPLBACKEND=Agg is already exported above, so the backend is identical.
def _datasensei_show(*args, **kwargs):
    return None


def _configure_pyplot(module) -> None:
    try:
        module.show = _datasensei_show
    except Exception:
        pass


class _PyplotLoader(importlib.abc.Loader):
    def __init__(self, loader) -> None:
        self._loader = loader

    def create_module(self, spec):
        return self._loader.create_module(spec)

    def exec_module(self, module) -> None:
        self._loader.exec_module(module)
        _configure_pyplot(module)

    def __getattr__(self, name):
        return getattr(self._loader, name)


class _PyplotFinder(importlib.abc.MetaPathFinder):
    def find_spec(self, fullname, path=None, target=None):
        if fullname != "matplotlib.pyplot":
            return None

        for finder in sys.meta_path:
            if finder is self or not hasattr(finder, "find_spec"):
                continue
            spec = finder.find_spec(fullname, path, target)
            if spec is not None and spec.loader is not None:
                spec.loader = _PyplotLoader(spec.loader)
                return spec

        return None


if "matplotlib.pyplot" in sys.modules:
    _configure_pyplot(sys.modules["matplotlib.pyplot"])
else:
    sys.meta_path.insert(0, _PyplotFinder())


# ---------------------------------------------------------------------------
# The learner's time limit
#
# RLIMIT_CPU alone could not express "your program ran too long": it counts CPU
# seconds, so at --cpus 0.50 a 10 CPU-second limit is 20 seconds of wall clock
# and Laravel's own wall-clock timeout always fired first. That timeout covers
# "docker run" plus the interpreter boot plus every "import pandas", so a
# perfectly good program was reported as an infinite loop whenever the machine
# was busy.
#
# This watchdog starts when the learner's first statement runs and stops when
# it returns, so only their code is on the clock. Time spent waiting for an
# input() answer is given back: thinking is not computing.
# ---------------------------------------------------------------------------

# Printed by the watchdog so Laravel can tell "the learner's program ran too
# long" apart from "the sandbox itself never finished starting".
TIMEOUT_MARKER = "__DATASENSEI_TIME_LIMIT__"

WALL_DEADLINE: float | None = None
WALL_SECONDS: float = 0.0
# Set from the job payload in a reused standby container.
JOB_WALL_SECONDS: float | None = None
WALL_EXPIRED = threading.Event()
_WALL_LOCK = threading.Lock()


def wall_credit(seconds: float) -> None:
    """Give the program back the time it spent waiting for the learner."""
    global WALL_DEADLINE

    if seconds <= 0:
        return

    with _WALL_LOCK:
        if WALL_DEADLINE is not None:
            WALL_DEADLINE += seconds


def _wall_watchdog() -> None:
    while True:
        with _WALL_LOCK:
            deadline = WALL_DEADLINE

        if deadline is None:
            return

        remaining = deadline - time.monotonic()

        if remaining <= 0:
            WALL_EXPIRED.set()
            try:
                sys.stderr.write(
                    "\n" + TIMEOUT_MARKER + "\n"
                    "Your program was still running after %d seconds, so the sandbox stopped it.\n"
                    "A loop that never ends, or a step that is too big for the learning sandbox, "
                    "is the usual cause.\n" % int(round(WALL_SECONDS))
                )
                sys.stderr.flush()
                sys.stdout.flush()
            except Exception:
                pass

            try:
                save_matplotlib_figures()
            except Exception:
                pass

            os._exit(124)

        time.sleep(min(0.25, max(0.02, remaining)))


def start_wall_clock(seconds: float) -> None:
    global WALL_DEADLINE, WALL_SECONDS

    seconds = float(seconds)
    if seconds <= 0:
        return

    with _WALL_LOCK:
        WALL_SECONDS = seconds
        WALL_DEADLINE = time.monotonic() + seconds

    thread = threading.Thread(target=_wall_watchdog, name="ds-wall-clock", daemon=True)
    thread.start()


def stop_wall_clock() -> None:
    global WALL_DEADLINE

    with _WALL_LOCK:
        WALL_DEADLINE = None


def wall_seconds_budget() -> float:
    """Seconds of running time the learner's own code may use."""
    if JOB_WALL_SECONDS is not None and JOB_WALL_SECONDS > 0:
        return float(JOB_WALL_SECONDS)

    for name in ("DS_WALL_SECONDS", "DS_CPU_SECONDS"):
        raw = os.environ.get(name)
        if raw:
            try:
                value = float(raw)
            except (TypeError, ValueError):
                continue
            if value > 0:
                return value

    return 10.0


def apply_cpu_limit(cpu_seconds: int) -> None:
    if resource is None:
        return

    # RLIMIT_CPU counts the whole process lifetime. A warm container has
    # already spent CPU on start-up and preloading, which must not be taken
    # from the learner's budget, so the limit is set relative to CPU used so far.
    try:
        used = int(time.process_time()) + 1 if STANDBY_MODE else 0
        limit = used + max(1, int(cpu_seconds))
        resource.setrlimit(resource.RLIMIT_CPU, (limit, limit + 1))
    except Exception:
        pass


def apply_resource_limits() -> None:
    if resource is None:
        # No POSIX limits here. The wall-clock watchdog still stops runaway
        # programs, which is what a learner actually hits.
        return

    if not STANDBY_MODE:
        apply_cpu_limit(int(os.environ.get("DS_CPU_SECONDS", "10") or 10))

    try:
        max_file_bytes = max(1_048_576, int(os.environ.get("DS_MAX_FILE_BYTES", "8388608")))
        resource.setrlimit(resource.RLIMIT_FSIZE, (max_file_bytes, max_file_bytes))
    except Exception:
        pass

    try:
        resource.setrlimit(resource.RLIMIT_NOFILE, (64, 64))
    except Exception:
        pass

    if os.environ.get("DS_USE_RLIMIT_AS") == "1":
        try:
            memory_bytes = max(134_217_728, int(os.environ.get("DS_MEMORY_BYTES", "536870912")))
            resource.setrlimit(resource.RLIMIT_AS, (memory_bytes, memory_bytes))
        except Exception:
            pass


apply_resource_limits()


# ------------------------------------------------------------
# Trusted paths
# ------------------------------------------------------------
def normalized_path(value: object) -> str:
    return str(value).replace("\\", "/")


def safe_resolve(value: object) -> Path | None:
    try:
        return Path(str(value)).resolve(strict=False)
    except Exception:
        return None


SITE_PACKAGE_PATHS = []

try:
    SITE_PACKAGE_PATHS.extend(site.getsitepackages())
except Exception:
    pass

try:
    user_site = site.getusersitepackages()
    if user_site:
        SITE_PACKAGE_PATHS.append(user_site)
except Exception:
    pass


TRUSTED_STACK_TOKENS = [
    "/site-packages/matplotlib/",
    "/site-packages/mpl_toolkits/",
    "/site-packages/PIL/",
    "/site-packages/Pillow/",
    "/site-packages/numpy/",
    "/site-packages/pandas/",
    "/site-packages/dateutil/",
    "/site-packages/kiwisolver/",
    "/site-packages/contourpy/",
    "/site-packages/cycler/",
    "/site-packages/fontTools/",
    "/site-packages/packaging/",
    "/usr/local/lib/python",
]


TRUSTED_DLOPEN_LIBRARY_KEYWORDS = [
    "matplotlib",
    "numpy",
    "pandas",
    "PIL",
    "pillow",
    "freetype",
    "png",
    "zlib",
    "jpeg",
    "stdc++",
    "gcc_s",
    "openblas",
    "lapack",
    "blas",
    # threadpoolctl (used by scikit-learn's KMeans, GaussianMixture, DBSCAN,
    # PCA...) opens the OpenMP/BLAS runtimes that numpy, scipy and scikit-learn
    # already loaded, to cap their thread count.
    # threadpoolctl walks the loaded libraries through libc's dl_iterate_phdr.
    # Only installed library code reaches this; learners cannot import ctypes.
    "libc.so",
    "gomp",
    "libomp",
    "iomp",
    "mkl",
    "blis",
    "flexiblas",
    "scipy",
    "sklearn",
    # Windows only, and only reachable from installed library code. CPython's
    # own ctypes module binds kernel32 while it is being imported, and numpy
    # imports ctypes, so on the unsafe local driver "import numpy" was blocked
    # outright and with it pandas, matplotlib, seaborn and scikit-learn. These
    # are the C runtime and core Win32 libraries those packages bind; no
    # networking library is listed, and a learner still cannot import ctypes.
    "kernel32",
    "msvcrt",
    "ucrtbase",
    "vcruntime",
    "api-ms-win-crt",
    "oleaut32",
    "ole32",
    "advapi32",
]


def called_from_trusted_code() -> bool:
    try:
        for frame in inspect.stack(context=0):
            filename = normalized_path(frame.filename)

            try:
                frame_path = Path(frame.filename).resolve(strict=False)
                if frame_path == RUNNER_FILE:
                    continue
            except Exception:
                pass

            for token in TRUSTED_STACK_TOKENS:
                if token in filename:
                    return True

            for package_path in SITE_PACKAGE_PATHS:
                if package_path and normalized_path(package_path) in filename:
                    return True

    except Exception:
        return False

    return False


def platform_temp_dir() -> Path | None:
    """Where this machine puts temporary files.

    In the container that is /tmp, which is already a root. On Windows it is
    the user's own Temp folder and matched nothing, so SciPy's MessageStream -
    reached by "import scipy.stats", which scikit-learn does - was refused
    while it opened its temp file and every scikit-learn lesson died.

    Resolved once here, before the audit hook is installed, because working it
    out involves opening files itself.
    """
    try:
        import tempfile

        return Path(tempfile.gettempdir()).resolve(strict=False)
    except Exception:
        return None


PLATFORM_TEMP_DIR = platform_temp_dir()


def path_is_inside(path: Path, root: Path) -> bool:
    try:
        path = path.resolve(strict=False)
        root = root.resolve(strict=False)
        return path == root or root in path.parents
    except Exception:
        return False


TRUSTED_LIBRARY_READ_FILES = {
    "/proc/self/maps",
    "/proc/cpuinfo",
    "/proc/meminfo",
    "/proc/self/cgroup",
    "/sys/fs/cgroup/cpu.max",
    "/sys/fs/cgroup/cpu/cpu.cfs_quota_us",
    "/sys/fs/cgroup/cpu/cpu.cfs_period_us",
}


def file_access_is_allowed(path_value: object, mode_value: object) -> bool:
    if path_value is None:
        return True

    if isinstance(path_value, int):
        return True

    # Runtime facts that numerical libraries read about their own process:
    # threadpoolctl (behind scikit-learn's KMeans, GaussianMixture, DBSCAN...)
    # lists the loaded BLAS/OpenMP libraries from /proc/self/maps, and joblib
    # reads the CPU quota. Read-only, only for installed library code, never
    # for the learner's own open() calls.
    if normalized_path(path_value) == "/dev/null":
        return True

    if normalized_path(path_value) in TRUSTED_LIBRARY_READ_FILES:
        mode_text = str(mode_value or "r").lower()
        is_read_only = not any(symbol in mode_text for symbol in ["w", "a", "+", "x"])
        return is_read_only and called_from_trusted_code()

    path = safe_resolve(path_value)

    if path is None:
        return False

    mode = str(mode_value or "r").lower()
    is_write = any(symbol in mode for symbol in ["w", "a", "+", "x"])

    roots = ALLOWED_WRITE_ROOTS if is_write else ALLOWED_READ_ROOTS
    parents = path.parents

    if any(path == root or root in parents for root in roots):
        return True

    # This machine's temp folder, for installed library code only. SciPy opens
    # a temp file while scipy.stats is imported and scikit-learn imports
    # scipy.stats, so on Windows - where the temp folder is not /tmp - every
    # scikit-learn lesson was refused. A learner's own temp write still falls
    # through to the roots above and is refused, so this does not hand student
    # code a writable corner outside its workspace.
    if PLATFORM_TEMP_DIR is not None:
        if path == PLATFORM_TEMP_DIR or PLATFORM_TEMP_DIR in parents:
            return called_from_trusted_code()

    return False


def _build_allowed_roots() -> tuple[list[Path], list[Path]]:
    # The roots never change during a run. Resolving them once instead of on
    # every open() matters: importing pandas or matplotlib opens hundreds of
    # files and each check used to resolve a dozen paths again.
    write_roots = [
        WORKSPACE,
        Path("/tmp").resolve(strict=False),
    ]

    # matplotlib's cache. It is given to us by the service, never by the
    # learner, whose code cannot reach os.environ at all. Without a writable
    # cache matplotlib rescans every font on the machine on every single run,
    # which on Windows is slow enough to spend the learner's whole time limit
    # before their first line executes.
    for variable in ("MPLCONFIGDIR", "XDG_CACHE_HOME"):
        location = os.environ.get(variable)

        if location:
            resolved = safe_resolve(location)

            if resolved is not None:
                write_roots.append(resolved)

    read_roots = [
        WORKSPACE,
        INPUT_DIR,
        Path("/tmp").resolve(strict=False),
        Path("/opt/datasensei").resolve(strict=False),
        # The runner's own folder. In the container that is /opt/datasensei
        # above, but the unsafe local driver runs it from the project tree,
        # where it matched no read root. Python's traceback machinery reads
        # the source of every frame it prints, so a blocked read here meant a
        # second sandbox error swallowed the first and the learner was shown a
        # wall of runner internals instead of their own mistake.
        RUNNER_FILE.parent,
        Path("/usr/local/lib").resolve(strict=False),
        Path("/usr/lib").resolve(strict=False),
        Path("/lib").resolve(strict=False),
        (Path(sys.base_prefix) / "lib").resolve(strict=False),
        (Path(sys.base_prefix) / "Lib").resolve(strict=False),
        Path("/usr/share/fonts").resolve(strict=False),
        Path("/etc/fonts").resolve(strict=False),
        # Read-only time zone data. pandas 3 opens /usr/share/zoneinfo/UTC while
        # it is being imported; with this blocked, "import pandas" (and with it
        # scikit-learn, seaborn and statsmodels) failed in every lesson.
        Path("/usr/share/zoneinfo").resolve(strict=False),
        Path("/usr/lib/zoneinfo").resolve(strict=False),
        Path("/usr/share/lib/zoneinfo").resolve(strict=False),
        Path("/etc/zoneinfo").resolve(strict=False),
        Path("/etc/localtime").resolve(strict=False),
        Path("/etc/timezone").resolve(strict=False),
    ]

    for package_path in SITE_PACKAGE_PATHS:
        try:
            read_roots.append(Path(package_path).resolve(strict=False))
        except Exception:
            pass

    return write_roots, read_roots


ALLOWED_WRITE_ROOTS, ALLOWED_READ_ROOTS = _build_allowed_roots()


# ------------------------------------------------------------
# Sandbox policy
# ------------------------------------------------------------
BLOCKED_IMPORT_ROOTS = {
    "os",
    "subprocess",
    "socket",
    "ctypes",
    "multiprocessing",
    "resource",
    "signal",
    "pty",
    "fcntl",
    "winreg",
    "importlib",
    "builtins",
    "pickle",
    "marshal",
    "shelve",
    "webbrowser",
    "http",
    "urllib",
    "ftplib",
    "telnetlib",
    "venv",
    "ensurepip",
    "requests",
    "httpx",
}

BLOCKED_EVENTS = {
    "subprocess.Popen",
    "os.system",
    "os.spawn",
    "os.posix_spawn",
    "os.fork",
    "os.forkpty",
    "os.exec",
    "os.kill",
    "pty.spawn",
    "socket.connect",
    "socket.bind",
    "socket.listen",
}


def block(event: str) -> None:
    raise RuntimeError(
        f"Sandbox policy blocked this operation: "
        f"Operation blocked by DataSensei sandbox policy: {event}"
    )


ON_WINDOWS = os.name == "nt"


def windows_system_library(raw_name: object, normalized_name: str) -> bool:
    """A Windows system DLL, loaded by bare name or from the system folder.

    Installed packages bind whatever Win32 library they happen to need:
    dateutil opens user32 to read time zone names, and pandas imports
    dateutil, so "import pandas" depended on it. Naming them one at a time
    turned every lesson into a bug report.

    This only ever applies to installed library code, which the caller has
    already established. It is also a narrower gate than it looks: the
    ctypes.dlopen audit event fires for ctypes only, so a compiled extension
    was always free to load its own DLLs without passing through here. What
    this permits, a .pyd could already do.
    """
    if not normalized_name:
        return False

    # A bare name ("user32", "kernel32.dll") is resolved by Windows' own DLL
    # search order; anything with a path is checked against the system folder.
    if "/" not in normalized_name and ":" not in normalized_name:
        return True

    resolved = safe_resolve(raw_name)
    system_root = os.environ.get("SystemRoot") or os.environ.get("windir")

    if resolved is None or not system_root:
        return False

    root = safe_resolve(system_root)

    return root is not None and (resolved == root or root in resolved.parents)


def ctypes_dlopen_is_allowed(args: tuple) -> bool:
    if not called_from_trusted_code():
        return False

    library_name = ""

    try:
        if args:
            library_name = normalized_path(args[0]).lower()
    except Exception:
        library_name = ""

    if library_name in ["", "none"]:
        return True

    if any(keyword.lower() in library_name for keyword in TRUSTED_DLOPEN_LIBRARY_KEYWORDS):
        return True

    if ON_WINDOWS and windows_system_library(args[0] if args else None, library_name):
        return True

    return False


def sandbox_audit_hook(event: str, args: tuple) -> None:
    if event == "import":
        try:
            module_name = str(args[0])
            root_name = module_name.split(".")[0]

            if root_name in BLOCKED_IMPORT_ROOTS and not called_from_trusted_code():
                block(f"import {root_name}")

        except Exception as exc:
            if isinstance(exc, RuntimeError):
                raise

    if event in BLOCKED_EVENTS:
        if not called_from_trusted_code():
            block(event)

    if event == "ctypes.dlopen":
        if ctypes_dlopen_is_allowed(args):
            return

        # Naming the library makes a blocked scientific package fixable from
        # the error message alone (it is the program's own request, no secret).
        block(f"ctypes.dlopen ({str(args[0])[:160] if args else '?'})")

    if event in {"ctypes.dlsym", "ctypes.call_function"}:
        if not called_from_trusted_code():
            block(event)

    if event == "open":
        try:
            path_value = args[0] if len(args) >= 1 else None
            mode_value = args[1] if len(args) >= 2 else "r"

            if not file_access_is_allowed(path_value, mode_value):
                block(f"open ({str(path_value)[:160]})")

        except Exception as exc:
            if isinstance(exc, RuntimeError):
                raise

            block("open")


sys.addaudithook(sandbox_audit_hook)


# ------------------------------------------------------------
# Source validation and bounded output
# ------------------------------------------------------------
FORBIDDEN_CALL_NAMES = {"__import__", "compile", "eval", "exec"}
FORBIDDEN_ATTRIBUTE_NAMES = {
    "__bases__",
    "__builtins__",
    "__code__",
    "__globals__",
    "__loader__",
    "__mro__",
    "__spec__",
    "__subclasses__",
}


class WorkspacePolicyVisitor(ast.NodeVisitor):
    def __init__(self, source_path: Path) -> None:
        self.source_path = source_path
        self.violations: list[str] = []

    def reject(self, node: ast.AST, message: str) -> None:
        line = getattr(node, "lineno", 1)
        relative = self.source_path.relative_to(WORKSPACE)
        self.violations.append(f"{relative}:{line}: {message}")

    def visit_Import(self, node: ast.Import) -> None:
        for alias in node.names:
            root_name = alias.name.split(".")[0]
            if root_name in BLOCKED_IMPORT_ROOTS:
                self.reject(node, f"import {root_name} is blocked")
        self.generic_visit(node)

    def visit_ImportFrom(self, node: ast.ImportFrom) -> None:
        root_name = (node.module or "").split(".")[0]
        if root_name in BLOCKED_IMPORT_ROOTS:
            self.reject(node, f"import from {root_name} is blocked")
        self.generic_visit(node)

    def visit_Call(self, node: ast.Call) -> None:
        if isinstance(node.func, ast.Name) and node.func.id in FORBIDDEN_CALL_NAMES:
            self.reject(node, f"{node.func.id}() is blocked")
        self.generic_visit(node)

    def visit_Attribute(self, node: ast.Attribute) -> None:
        if node.attr in FORBIDDEN_ATTRIBUTE_NAMES:
            self.reject(node, f"unsafe introspection attribute {node.attr} is blocked")
        if isinstance(node.value, ast.Name) and node.value.id == "sys" and node.attr in {"modules", "path"}:
            self.reject(node, f"sys.{node.attr} is blocked")
        self.generic_visit(node)


def validate_workspace_sources() -> None:
    violations: list[str] = []

    for source_path in WORKSPACE.rglob("*.py"):
        if OUTPUT_DIR in source_path.parents:
            continue

        try:
            source = source_path.read_text(encoding="utf-8")
            tree = ast.parse(source, filename=str(source_path))
        except (OSError, UnicodeError, SyntaxError):
            # Syntax and encoding errors are reported naturally by Python when the
            # affected file is executed or imported.
            continue

        visitor = WorkspacePolicyVisitor(source_path)
        visitor.visit(tree)
        violations.extend(visitor.violations)

    if violations:
        joined = "\n".join(f"- {violation}" for violation in violations[:20])
        raise RuntimeError(f"Sandbox policy rejected workspace source:\n{joined}")


class LimitedTextStream:
    def __init__(self, stream, max_bytes: int) -> None:
        self.stream = stream
        self.remaining = max(0, max_bytes)
        self.truncated = False

    def _emit(self, text: str) -> None:
        if text:
            self.stream.write(text)

    @property
    def encoding(self):
        return getattr(self.stream, "encoding", "utf-8")

    def write(self, value) -> int:
        text = str(value)
        encoded = text.encode("utf-8", errors="replace")

        if self.remaining <= 0:
            if not self.truncated:
                self._emit("\n[Output truncated by DataSensei sandbox]\n")
                self.truncated = True
            return len(text)

        chunk = encoded[: self.remaining]
        self.remaining -= len(chunk)
        self._emit(chunk.decode("utf-8", errors="ignore"))

        if len(chunk) < len(encoded) and not self.truncated:
            self._emit("\n[Output truncated by DataSensei sandbox]\n")
            self.truncated = True

        return len(text)

    def flush(self) -> None:
        self.stream.flush()

    def isatty(self) -> bool:
        return False

    def fileno(self) -> int:
        return self.stream.fileno()


def force_utf8_output() -> None:
    """Everything the program prints leaves here as UTF-8.

    Laravel turns the output into JSON, and json_encode refuses anything that
    is not valid UTF-8. The container is UTF-8 throughout, but on Windows a
    pipe is opened in the console code page (cp1252), so a plain "±" in a
    lesson's f-string left as the single byte 0xB1 and the whole Run failed
    with "Malformed UTF-8 characters" before the learner saw a line of it.
    Characters the target cannot hold are replaced rather than raised.
    """
    for stream in (sys.stdout, sys.stderr):
        try:
            stream.reconfigure(encoding="utf-8", errors="replace")
        except Exception:
            pass


def install_output_limits() -> None:
    force_utf8_output()
    total_bytes = max(65_536, int(os.environ.get("DS_MAX_OUTPUT_BYTES", "8388608")))
    stdout_bytes = max(32_768, total_bytes * 3 // 4)
    stderr_bytes = max(32_768, total_bytes - stdout_bytes)
    sys.stdout = LimitedTextStream(sys.stdout, stdout_bytes)
    sys.stderr = LimitedTextStream(sys.stderr, stderr_bytes)


class DataSenseiInputRequired(EOFError):
    """Signals that the IDE must collect one more value from the learner."""

    def __init__(self, prompt: str) -> None:
        super().__init__("DataSensei is waiting for program input.")
        self.prompt = prompt


# How many input() calls were satisfied from stdin before the program asked
# for one more. The IDE compares this with the number of answers it sent: if
# the program consumed fewer, stdin is not reaching the sandbox and replaying
# would loop forever.
_inputs_consumed = 0


def inputs_consumed() -> int:
    return _inputs_consumed


# Graded runs (coding challenges) compare stdout exactly against the expected
# output. The lessons teach input("Enter your name: "), and Python prints that
# prompt to stdout, so a correct program failed every stdin test with output
# like "Enter a: Enter b: 5". When grading, the prompt is simply not printed.
# The IDE never sets this, so prompts still appear there as normal.
QUIET_INPUT_PROMPTS = os.environ.get("DS_QUIET_INPUT_PROMPTS") == "1"


def quiet_input_prompts() -> None:
    """Wrap whatever input() is installed so its prompt is never printed."""
    if not QUIET_INPUT_PROMPTS:
        return

    installed = builtins.input

    def input_without_prompt(prompt="") -> str:
        return installed("")

    builtins.input = input_without_prompt


def install_interactive_input_bridge() -> None:
    """
    Preserve normal input() behavior outside the IDE. For IDE runs, report an
    unmet prompt to Laravel without exposing a learner-facing EOF traceback.
    The browser can then collect one value and replay the isolated program with
    every answer supplied so far.
    """
    try:
        _install_input_bridge()
    finally:
        quiet_input_prompts()


def _install_input_bridge() -> None:
    if SESSION_FEED is not None:
        install_session_input_bridge()
        return

    if os.environ.get("DS_INTERACTIVE_INPUT") != "1":
        return

    def datasensei_input(prompt="") -> str:
        global _inputs_consumed
        prompt_text = str(prompt)

        if prompt_text:
            sys.stdout.write(prompt_text)
            sys.stdout.flush()

        try:
            line = sys.stdin.readline()
        except (OSError, ValueError):
            # A closed or detached stdin is indistinguishable from "no answer
            # yet" for the learner, so report it the same way.
            line = ""

        if line == "":
            raise DataSenseiInputRequired(prompt_text)

        _inputs_consumed += 1

        return line.rstrip("\r\n")

    builtins.input = datasensei_input


class SessionFeed:
    """
    Standard input of a warm-sandbox job. Laravel writes the first lines into
    the job and appends every later answer to SESSION_STDIN with "docker exec".
    An interactive input() announces the prompt once and then waits for the
    next line, so the running program simply continues. Waiting sleeps, which
    costs no CPU budget; an abandoned session ends itself after idle_seconds.
    """

    def __init__(self, initial: str, interactive: bool, idle_seconds: int) -> None:
        self.buffer = initial
        self.position = 0
        self.interactive = interactive
        self.idle_seconds = max(5, idle_seconds)
        self.file_offset = 0
        self.pending = b""

    def _load_appended(self) -> None:
        # stat() first: it is far cheaper than open() (which also runs the
        # sandbox audit hook), and a program may wait here for minutes. The
        # wait must not eat the program's CPU allowance.
        try:
            if os.stat(SESSION_STDIN).st_size <= self.file_offset:
                return
        except OSError:
            return

        try:
            with open(SESSION_STDIN, "rb") as handle:
                handle.seek(self.file_offset)
                data = handle.read()
        except OSError:
            return

        if not data:
            return

        self.file_offset += len(data)
        data = self.pending + data
        # Hold back an incomplete UTF-8 sequence until the rest arrives.
        text = data.decode("utf-8", errors="ignore")
        consumed = len(text.encode("utf-8"))
        self.pending = data[consumed:] if consumed < len(data) and len(data) - consumed < 4 else b""
        self.buffer += text

    def readline(self, block: bool = False, prompt: str = "") -> str:
        announced = False
        waited_since = time.monotonic()

        while True:
            self._load_appended()
            newline = self.buffer.find("\n", self.position)

            if newline >= 0:
                line = self.buffer[self.position:newline + 1]
                self.position = newline + 1
                return line

            if not (block and self.interactive):
                line = self.buffer[self.position:]
                self.position = len(self.buffer)
                return line

            if not announced:
                emit_input_required(prompt)
                announced = True

            waited = time.monotonic() - waited_since
            if waited > self.idle_seconds:
                raise DataSenseiInputRequired(prompt)

            # Snappy for a quick answer, relaxed for a long think.
            pause = 0.01 if waited < 5 else 0.03
            time.sleep(pause)
            # The learner was thinking, not computing.
            wall_credit(pause)

    def read_available(self) -> str:
        self._load_appended()
        text = self.buffer[self.position:]
        self.position = len(self.buffer)
        return text


class SessionStdin(io.TextIOBase):
    """sys.stdin for a job: never blocks, exactly like the piped stdin it replaces."""

    def __init__(self, feed: SessionFeed) -> None:
        super().__init__()
        self._feed = feed

    @property
    def encoding(self):
        return "utf-8"

    def readable(self) -> bool:
        return True

    def readline(self, size: int = -1) -> str:
        return self._feed.readline(block=False)

    def read(self, size: int = -1) -> str:
        return self._feed.read_available()

    def isatty(self) -> bool:
        return False


SESSION_FEED: SessionFeed | None = None


def install_session_input_bridge() -> None:
    feed = SESSION_FEED
    sys.stdin = SessionStdin(feed)

    def datasensei_input(prompt="") -> str:
        global _inputs_consumed
        prompt_text = str(prompt)

        if prompt_text:
            sys.stdout.write(prompt_text)
            sys.stdout.flush()

        line = feed.readline(block=True, prompt=prompt_text)

        if line == "":
            if feed.interactive:
                raise DataSenseiInputRequired(prompt_text)
            raise EOFError("EOF when reading a line")

        _inputs_consumed += 1

        return line.rstrip("\r\n")

    builtins.input = datasensei_input


_state_seq = 0


def write_session_state(state: str, **extra) -> None:
    """
    The attach helper polls this file. It is replaced atomically, and "seq"
    grows with every input request, so an answer that was fed is never
    confused with the request it answered.
    """
    global _state_seq

    for stream in (sys.stdout, sys.stderr):
        try:
            stream.flush()
        except Exception:
            pass

    if state in ("waiting", "exited"):
        _state_seq += 1

    payload = {"state": state, "seq": _state_seq}
    payload.update(extra)
    temporary = str(SESSION_STATE) + ".tmp"

    try:
        with open(temporary, "w", encoding="utf-8") as handle:
            json.dump(payload, handle)
        os.replace(temporary, SESSION_STATE)
    except OSError:
        pass


def emit_input_required(prompt: str) -> None:
    if SESSION_FEED is not None:
        write_session_state("waiting", prompt=prompt, consumed=inputs_consumed())
        return

    encoded_prompt = base64.b64encode(prompt.encode("utf-8")).decode("ascii")
    # Start on a fresh line even when student code wrote an unterminated warning
    # to stderr immediately before asking for input.
    # Written to the real stderr: a program that already used up its stderr
    # allowance must still be able to ask for input.
    try:
        sys.stderr.flush()
    except Exception:
        pass

    print(f"\n{INPUT_REQUIRED_MARKER}{encoded_prompt}", file=sys.__stderr__, flush=True)
    # Separate line, so the prompt marker keeps its existing payload format.
    print(f"{INPUTS_CONSUMED_MARKER}{inputs_consumed()}", file=sys.__stderr__, flush=True)


# ------------------------------------------------------------
# Output cleanup and plot saving
# ------------------------------------------------------------
def clean_previous_outputs() -> None:
    try:
        OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

        for file in OUTPUT_DIR.iterdir():
            if file.is_file() and file.name.lower().endswith(
                (".png", ".jpg", ".jpeg", ".svg", ".pdf", ".json")
            ):
                file.unlink(missing_ok=True)

    except Exception:
        pass


def emit_plot_base64(path: Path) -> None:
    try:
        max_plot_bytes = int(os.environ.get("DS_MAX_PLOT_BYTES", "1500000"))
        data = path.read_bytes()

        if len(data) > max_plot_bytes:
            print("[Plot omitted by sandbox size limit]", file=sys.stderr)
            return

        encoded = base64.b64encode(data).decode("ascii")
        print(f"__PLOT_BASE64__:{encoded}:__END_PLOT__")

    except Exception:
        pass


def save_matplotlib_figures() -> list[str]:
    saved_files = []

    # A program that never imported pyplot has no figures. Importing it here
    # only to find that out used to cost every run most of a second.
    if "matplotlib.pyplot" not in sys.modules:
        return saved_files

    try:
        import matplotlib.pyplot as plt

        figure_numbers = plt.get_fignums()
        max_plots = int(os.environ.get("DS_MAX_PLOTS", "4"))

        for index, figure_number in enumerate(figure_numbers[:max_plots], start=1):
            figure = plt.figure(figure_number)
            output_file = OUTPUT_DIR / f"figure_{index}.png"

            figure.savefig(
                output_file,
                format="png",
                dpi=150,
                bbox_inches="tight",
            )

            saved_files.append(str(output_file.relative_to(WORKSPACE)))
            emit_plot_base64(output_file)

        plt.close("all")

        metadata_file = OUTPUT_DIR / "outputs.json"
        metadata_file.write_text(
            json.dumps({"figures": saved_files}, indent=2),
            encoding="utf-8",
        )

    except Exception:
        pass

    return saved_files


# ------------------------------------------------------------
# Main execution
# ------------------------------------------------------------
def resolve_script_argument() -> Path:
    args = sys.argv[1:]

    if not args:
        return WORKSPACE / "main.py"

    if args[0] in {"python", "python3", "py"}:
        args = args[1:]

    if not args:
        return WORKSPACE / "main.py"

    script_path = Path(args[0])

    if not script_path.is_absolute():
        script_path = WORKSPACE / script_path

    return script_path.resolve(strict=False)


def show_available_workspace_files() -> None:
    try:
        files = sorted(
            str(path.relative_to(WORKSPACE))
            for path in WORKSPACE.rglob("*")
            if path.is_file()
        )

        if files:
            print("\nAvailable files in /workspace:", file=sys.stderr)
            for file in files[:100]:
                print(f"- {file}", file=sys.stderr)

    except Exception:
        pass


def _safe_job_target(relative: str) -> Path | None:
    candidate = (WORKSPACE / str(relative).replace("\\", "/").lstrip("/")).resolve(strict=False)
    return candidate if path_is_inside(candidate, WORKSPACE) and candidate != WORKSPACE else None


def wait_for_job() -> dict | None:
    """Standby: preload libraries, then wait until Laravel delivers one job."""
    try:
        SESSION_DIR.mkdir(parents=True, exist_ok=True)
    except Exception:
        pass

    # Spending RAM for speed: the heavy libraries are imported while the
    # container is idle, so "import pandas" in a lesson is instant later.
    skipped: list[str] = []

    for name in [part.strip() for part in os.environ.get("DS_PRELOAD", "").split(",") if part.strip()]:
        # A learner's job always wins over the remaining preloads. Laravel only
        # hands jobs to containers that reported READY, so this is a second
        # line of defence against a run waiting for imports it may not need.
        if SESSION_JOB.exists():
            break
        try:
            __import__(name)
        except ModuleNotFoundError as exc:
            # The library simply is not in this image. Nothing is broken: the
            # learner's own "import seaborn" would fail the same way on the
            # classic path. Refusing the container here used to empty the whole
            # warm pool over one optional package, which sent every single run
            # down the slow "docker run" path.
            skipped.append(f"{name} ({exc.__class__.__name__})")
        except BaseException as exc:
            # A half-imported C extension cannot be imported again in this
            # process: the learner would get a confusing secondary error
            # ("partially initialized module 'pandas' ... _pandas_datetime_CAPI")
            # instead of the real one. Such a container must never take a job.
            print(f"__DATASENSEI_PRELOAD_FAILED__:{name}: {type(exc).__name__}: {exc}", file=sys.stderr, flush=True)
            return None

    if skipped:
        print("__DATASENSEI_PRELOAD_SKIPPED__:" + ", ".join(skipped), file=sys.stderr, flush=True)

    print(f"__DATASENSEI_READY__:{RUNNER_PROTOCOL}", file=sys.stderr, flush=True)

    deadline = time.monotonic() + max(30, int(os.environ.get("DS_STANDBY_SECONDS", "1800") or 1800))

    partial_since = None

    while time.monotonic() < deadline:
        if SESSION_JOB.exists():
            try:
                job = json.loads(SESSION_JOB.read_text(encoding="utf-8"))
            except Exception:
                # Still being written ("docker exec ... dd of=job" is not
                # atomic). Give the writer a few seconds to finish.
                partial_since = partial_since or time.monotonic()
                if time.monotonic() - partial_since > 10:
                    return {}
                time.sleep(0.005)
                continue

            try:
                SESSION_JOB.unlink()
            except Exception:
                pass

            redirect_output_to_session_files()
            # The execution clock starts here, not when the job was sent.
            write_session_state("started")

            return job if isinstance(job, dict) else {}

        time.sleep(0.02)

    return None


def redirect_output_to_session_files() -> None:
    """
    From here on everything the program prints goes to two files in the
    container's own /tmp, where the attach helper reads it. Unlike the Docker
    log this keeps unterminated text (a prompt without a newline) and needs no
    log follower on the host.
    """
    for path, descriptor in ((SESSION_OUT, 1), (SESSION_ERR, 2)):
        try:
            target = os.open(str(path), os.O_CREAT | os.O_WRONLY | os.O_APPEND, 0o600)
            os.dup2(target, descriptor)
            os.close(target)
        except OSError:
            pass


def finish_session(exit_code: int) -> None:
    write_session_state("exited", exit_code=exit_code)

    # When this process ends the container stops and a helper that is still
    # reading would be killed with it, so wait briefly for its acknowledgement.
    deadline = time.monotonic() + 3
    while time.monotonic() < deadline and not SESSION_ACK.exists():
        time.sleep(0.004)


def prepare_job(job: dict) -> Path | None:
    global SESSION_FEED

    for entry in job.get("dirs") or []:
        target = _safe_job_target(entry)
        if target is not None:
            target.mkdir(parents=True, exist_ok=True)

    for item in job.get("files") or []:
        target = _safe_job_target(item.get("path", ""))
        if target is None:
            continue
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_bytes(base64.b64decode(item.get("b64", "")))
        try:
            target.chmod(0o644)
        except Exception:
            pass

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

    SESSION_FEED = SessionFeed(
        str(job.get("stdin") or ""),
        bool(job.get("interactive")),
        int(job.get("idle_seconds") or 120),
    )
    apply_cpu_limit(int(job.get("cpu_seconds") or 10))

    # A standby container is reused, so the budget travels with the job rather
    # than with the container's environment.
    global JOB_WALL_SECONDS
    JOB_WALL_SECONDS = float(job.get("wall_seconds") or job.get("cpu_seconds") or 10)

    # Likewise for graded runs: the standby container was started before the
    # grader asked for quiet prompts, so the setting arrives with the job.
    global QUIET_INPUT_PROMPTS
    QUIET_INPUT_PROMPTS = bool(job.get("quiet_prompts"))

    return _safe_job_target(job.get("entry") or "main.py")


def main() -> int:
    if STANDBY_MODE:
        job = wait_for_job()
        if job is None:
            return 0
        script_path = prepare_job(job)
        if script_path is None:
            print("Sandbox policy blocked a script outside the workspace.", file=sys.stderr)
            finish_session(126)
            return 126
        exit_code = run_script(script_path)
        finish_session(exit_code)
        return exit_code

    return run_script(resolve_script_argument())


# Libraries lesson examples reference that the sandbox does not install, with
# the reason in words a learner can act on. Anything not listed falls back to
# the generic sentence below.
UNAVAILABLE_LIBRARIES = {
    "tensorflow": "TensorFlow, a deep-learning framework",
    "torch": "PyTorch, a deep-learning framework",
    "keras": "Keras, a deep-learning framework",
    "transformers": "Hugging Face Transformers",
    "gymnasium": "Gymnasium, a reinforcement-learning toolkit",
    "shap": "SHAP, a model-explanation library",
    "prophet": "Prophet, a forecasting library",
    "plotly": "Plotly, an interactive charting library",
    "pyspark": "PySpark, which needs a Spark cluster",
    "kafka": "the Kafka client, which needs a Kafka broker",
    "airflow": "Apache Airflow, which needs a scheduler",
    "mlflow": "MLflow, which needs a tracking server",
    "delta": "Delta Lake, which needs Spark",
    "boto3": "the AWS SDK, which needs network access",
    "google": "the Google Cloud SDK, which needs network access",
    "pymongo": "the MongoDB client, which needs a database server",
    "redis": "the Redis client, which needs a Redis server",
    "sqlalchemy": "SQLAlchemy, a database toolkit",
    "psycopg2": "the PostgreSQL driver, which needs a database server",
    "fastapi": "FastAPI, a web framework",
    "pydantic": "Pydantic, a data-validation library",
    "requests": "Requests; the sandbox has no network access",
}


def explain_missing_module(exc: ModuleNotFoundError) -> str:
    root = (getattr(exc, "name", "") or "").split(".")[0]
    described = UNAVAILABLE_LIBRARIES.get(root)

    if described:
        return (
            f"This example uses {described}, which is not installed in the "
            "learning sandbox. The code is here to read and study rather than "
            "to run. Nothing is wrong with what you wrote."
        )

    return (
        f"This example needs the '{root}' library, which is not installed in "
        "the learning sandbox. If a lesson expects it, tell your instructor. "
        "Nothing is wrong with what you wrote."
    )


# Loading these is sandbox start-up, not the learner's work. On Windows,
# where every file the interpreter opens is scanned before it is handed over,
# "import pandas" alone can take longer than the whole time limit.
PRELOADABLE_LIBRARIES = [
    "numpy",
    "pandas",
    "matplotlib",
    "matplotlib.pyplot",
    "seaborn",
    "scipy",
    "scipy.stats",
    "sklearn",
    "statsmodels",
    "statsmodels.api",
    "joblib",
]


def preload_declared_libraries(script_path: Path) -> None:
    """Import the heavy libraries the program asks for, before its clock runs.

    A warm standby container already has these resident, which is why the
    warm path never showed this. The classic and local paths start a fresh
    interpreter for every Run, so the learner was being charged for loading
    pandas, seaborn, matplotlib and scipy before their first statement -
    frequently the entire budget, reported to them as an endless loop.

    Only what the program actually names is loaded, so a plain "print" program
    still starts instantly.
    """
    if STANDBY_MODE:
        return

    try:
        source = script_path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return

    for name in PRELOADABLE_LIBRARIES:
        root = name.split(".")[0]

        declared = any(
            line.lstrip().startswith(("import " + root, "from " + root))
            for line in source.splitlines()
        )

        if not declared:
            continue

        try:
            __import__(name)
        except BaseException:
            # Not installed, or broken here. The program's own import will
            # raise and be reported properly; this is only a head start.
            pass


def build_font_index_once() -> None:
    """Build matplotlib's font index before the learner's clock starts.

    The first import of matplotlib on a machine walks every installed font and
    writes an index. On Windows that can take longer than the learner's whole
    time limit, and being stopped part-way meant the index was never finished:
    every later run began it again and was killed at the same point. Doing it
    here, before start_wall_clock(), charges it to sandbox start-up - which is
    what the start-up allowance is for - and it happens once per machine.
    """
    cache = os.environ.get("MPLCONFIGDIR")

    if not cache:
        return

    try:
        if any(Path(cache).glob("fontlist-v*.json")):
            return
    except Exception:
        return

    try:
        import matplotlib

        matplotlib.use("Agg")

        from matplotlib import font_manager

        # Touching the manager is what triggers the scan and the write.
        font_manager.fontManager
    except Exception:
        # A machine without matplotlib, or an index that cannot be written,
        # is not a reason to refuse the run: the program may not plot at all.
        pass


def run_script(script_path: Path) -> int:

    if not path_is_inside(script_path, WORKSPACE):
        print("Sandbox policy blocked a script outside the workspace.", file=sys.stderr)
        return 126

    if not script_path.exists():
        print(f"Python file not found: {script_path}", file=sys.stderr)
        show_available_workspace_files()
        return 1

    clean_previous_outputs()
    install_output_limits()
    install_interactive_input_bridge()

    try:
        validate_workspace_sources()
    except RuntimeError as exc:
        print(str(exc), file=sys.stderr)
        return 126

    exit_code = 0

    try:
        os.chdir(WORKSPACE)
        # "python file.py" puts the file's folder first on sys.path; run_path()
        # does not, and the first entry was the runner's own folder instead, so
        # "from helper import ..." could never find a sibling file of the
        # workspace. Replace that entry, which also keeps the trusted runner
        # module out of the program's import path.
        script_dir = str(script_path.parent)
        if sys.path and safe_resolve(sys.path[0]) == RUNNER_FILE.parent:
            sys.path[0] = script_dir
        elif script_dir not in sys.path:
            sys.path.insert(0, script_dir)
        preload_declared_libraries(script_path)
        build_font_index_once()
        start_wall_clock(wall_seconds_budget())
        runpy.run_path(str(script_path), run_name="__main__")

    except SystemExit as exc:
        try:
            exit_code = int(exc.code or 0)
        except Exception:
            exit_code = 1

    except DataSenseiInputRequired as exc:
        emit_input_required(exc.prompt)
        exit_code = 75

    except ModuleNotFoundError as exc:
        # Some lesson examples are written against tools the learning sandbox
        # deliberately does not carry: deep-learning frameworks, cloud SDKs,
        # database and streaming clients that would need a server and a
        # network the sandbox does not give them. A raw traceback reads as
        # "your code is broken", which it is not, so say what actually
        # happened and leave the traceback underneath for the curious.
        print(explain_missing_module(exc), file=sys.stderr)
        traceback.print_exc()
        exit_code = 1

    except BaseException:
        traceback.print_exc()
        exit_code = 1

    finally:
        # Saving figures and flushing output belong to the run, not to the
        # learner's time limit.
        stop_wall_clock()
        save_matplotlib_figures()

    return exit_code


if __name__ == "__main__":
    raise SystemExit(main())
