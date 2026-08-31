#!/usr/bin/env python3

import ast
import base64
import inspect
import json
import os
import runpy
import resource
import shutil
import site
import sys
import traceback
from pathlib import Path


RUNNER_FILE = Path(__file__).resolve(strict=False)
WORKSPACE = Path(os.environ.get("DS_WORKSPACE", "/workspace")).resolve(strict=False)
INPUT_DIR = Path(os.environ.get("DS_INPUT", "/input")).resolve(strict=False)
OUTPUT_DIR = WORKSPACE / "datasensei_outputs"

os.environ.setdefault("MPLBACKEND", "Agg")
os.environ.setdefault("MPLCONFIGDIR", "/tmp/matplotlib")
os.environ.setdefault("XDG_CACHE_HOME", "/tmp/.cache")
os.environ.setdefault("PYTHONNOUSERSITE", "1")

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
try:
    import matplotlib

    matplotlib.use("Agg", force=True)

    import matplotlib.pyplot as plt

    def datasensei_show(*args, **kwargs):
        return None

    plt.show = datasensei_show

except Exception:
    pass


def apply_resource_limits() -> None:
    try:
        cpu_seconds = max(1, int(os.environ.get("DS_CPU_SECONDS", "10")))
        resource.setrlimit(resource.RLIMIT_CPU, (cpu_seconds, cpu_seconds + 1))
    except Exception:
        pass

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


def path_is_inside(path: Path, root: Path) -> bool:
    try:
        path = path.resolve(strict=False)
        root = root.resolve(strict=False)
        return path == root or root in path.parents
    except Exception:
        return False


def file_access_is_allowed(path_value: object, mode_value: object) -> bool:
    if path_value is None:
        return True

    if isinstance(path_value, int):
        return True

    path = safe_resolve(path_value)

    if path is None:
        return False

    mode = str(mode_value or "r").lower()
    is_write = any(symbol in mode for symbol in ["w", "a", "+", "x"])

    allowed_write_roots = [
        WORKSPACE,
        Path("/tmp").resolve(strict=False),
    ]

    allowed_read_roots = [
        WORKSPACE,
        INPUT_DIR,
        Path("/tmp").resolve(strict=False),
        Path("/opt/datasensei").resolve(strict=False),
        Path("/usr/local/lib").resolve(strict=False),
        Path("/usr/lib").resolve(strict=False),
        Path("/lib").resolve(strict=False),
        Path("/usr/share/fonts").resolve(strict=False),
        Path("/etc/fonts").resolve(strict=False),
    ]

    for package_path in SITE_PACKAGE_PATHS:
        try:
            allowed_read_roots.append(Path(package_path).resolve(strict=False))
        except Exception:
            pass

    if is_write:
        return any(path_is_inside(path, root) for root in allowed_write_roots)

    return any(path_is_inside(path, root) for root in allowed_read_roots)


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

        block("ctypes.dlopen")

    if event in {"ctypes.dlsym", "ctypes.call_function"}:
        if not called_from_trusted_code():
            block(event)

    if event == "open":
        try:
            path_value = args[0] if len(args) >= 1 else None
            mode_value = args[1] if len(args) >= 2 else "r"

            if not file_access_is_allowed(path_value, mode_value):
                block("open")

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

    @property
    def encoding(self):
        return getattr(self.stream, "encoding", "utf-8")

    def write(self, value) -> int:
        text = str(value)
        encoded = text.encode("utf-8", errors="replace")

        if self.remaining <= 0:
            if not self.truncated:
                self.stream.write("\n[Output truncated by DataSensei sandbox]\n")
                self.truncated = True
            return len(text)

        chunk = encoded[: self.remaining]
        self.remaining -= len(chunk)
        self.stream.write(chunk.decode("utf-8", errors="ignore"))

        if len(chunk) < len(encoded) and not self.truncated:
            self.stream.write("\n[Output truncated by DataSensei sandbox]\n")
            self.truncated = True

        return len(text)

    def flush(self) -> None:
        self.stream.flush()

    def isatty(self) -> bool:
        return False

    def fileno(self) -> int:
        return self.stream.fileno()


def install_output_limits() -> None:
    total_bytes = max(65_536, int(os.environ.get("DS_MAX_OUTPUT_BYTES", "8388608")))
    stdout_bytes = max(32_768, total_bytes * 3 // 4)
    stderr_bytes = max(32_768, total_bytes - stdout_bytes)
    sys.stdout = LimitedTextStream(sys.stdout, stdout_bytes)
    sys.stderr = LimitedTextStream(sys.stderr, stderr_bytes)


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

    try:
        import matplotlib

        matplotlib.use("Agg", force=True)

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


def main() -> int:
    script_path = resolve_script_argument()

    if not path_is_inside(script_path, WORKSPACE):
        print("Sandbox policy blocked a script outside the workspace.", file=sys.stderr)
        return 126

    if not script_path.exists():
        print(f"Python file not found: {script_path}", file=sys.stderr)
        show_available_workspace_files()
        return 1

    clean_previous_outputs()
    install_output_limits()

    try:
        validate_workspace_sources()
    except RuntimeError as exc:
        print(str(exc), file=sys.stderr)
        return 126

    exit_code = 0

    try:
        os.chdir(WORKSPACE)
        runpy.run_path(str(script_path), run_name="__main__")

    except SystemExit as exc:
        try:
            exit_code = int(exc.code or 0)
        except Exception:
            exit_code = 1

    except BaseException:
        traceback.print_exc()
        exit_code = 1

    finally:
        save_matplotlib_figures()

    return exit_code


if __name__ == "__main__":
    raise SystemExit(main())
