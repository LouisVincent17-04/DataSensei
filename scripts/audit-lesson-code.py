#!/usr/bin/env python3
"""
Runs every "Try in Compiler" Python example of the lesson seeders through the
real sandbox runner and reports the ones that fail or are slow.

    python scripts/audit-lesson-code.py                 # uses this Python
    python scripts/audit-lesson-code.py --python C:\\path\\to\\python.exe --module 14

The Python you point it at needs the same libraries as the runner image
(numpy, pandas, matplotlib, scipy, scikit-learn, seaborn, statsmodels), and the
runner needs Linux, macOS or WSL. On Windows, run it inside the runner image:

    docker run --rm --entrypoint python -v "%cd%":/app:ro datasensei-python-runner:latest /app/scripts/audit-lesson-code.py
Examples that call input() are given no answers, so they end with EOFError;
that is expected. Run this after editing lesson code or upgrading a library.
"""
from __future__ import annotations

import argparse
import glob
import os
import re
import shutil
import subprocess
import sys
import tempfile
import time
from concurrent.futures import ThreadPoolExecutor
from html.parser import HTMLParser
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
RUNNER = ROOT / "docker" / "python-runner" / "datasensei_runner.py"


class CodeWindows(HTMLParser):
    def __init__(self, text: str) -> None:
        super().__init__(convert_charrefs=True)
        self.blocks: list[dict] = []
        self.window: dict | None = None
        self.parts: list[str] | None = None
        self.depth = 0
        self.label: list[str] | None = None
        self.feed(text)

    def handle_starttag(self, tag, attrs):
        attributes = dict(attrs)
        classes = (attributes.get("class") or "").split()
        if self.parts is not None:
            self.depth += tag == "div"
            return
        if tag == "div" and "code-window" in classes:
            self.window = {"label": None, "button": False, "line": self.getpos()[0]}
        if self.window is None:
            return
        if tag == "span" and self.window["label"] is None:
            self.label = []
        if tag == "button" and "launchIDE" in (attributes.get("onclick") or ""):
            self.window["button"] = True
        if tag == "div" and "code-content" in classes:
            self.parts, self.depth = [], 1

    def handle_endtag(self, tag):
        if self.parts is not None:
            if tag == "div":
                self.depth -= 1
                if self.depth == 0:
                    self.window["code"] = "".join(self.parts).replace("\r", "").strip()
                    self.blocks.append(self.window)
                    self.parts, self.window = None, None
            return
        if tag == "span" and self.label is not None and self.window is not None:
            self.window["label"] = "".join(self.label).strip()
            self.label = None

    def handle_data(self, data):
        if self.parts is not None:
            self.parts.append(data)
        elif self.label is not None:
            self.label.append(data)


def lesson_blocks(module: int | None):
    pattern = str(ROOT / "database" / "seeders" / "Module*LessonsSeeder.php")
    for path in sorted(glob.glob(pattern), key=lambda p: int(re.search(r"Module(\d+)L", p).group(1))):
        number = int(re.search(r"Module(\d+)L", path).group(1))
        if module is not None and number != module:
            continue
        source = Path(path).read_text(encoding="utf-8")
        for match in re.finditer(r"<<<'HTML'\r?\n(.*?)\r?\nHTML;", source, re.S):
            first_line = source[: match.start(1)].count("\n")
            for block in CodeWindows(match.group(1)).blocks:
                label = (block["label"] or "").upper()
                if block["button"] and not label.startswith("SQL"):
                    yield {"module": number, "line": first_line + block["line"], "label": block["label"] or "", "code": block["code"]}


def run(python: str, block: dict) -> dict:
    workspace = tempfile.mkdtemp(prefix="lesson-audit-")
    Path(workspace, "practice.py").write_text(block["code"], encoding="utf-8")
    # The sandbox lets a program read its workspace and the temp folder only,
    # so the runner is used from a temp copy (the image keeps it in /opt).
    runner = Path(tempfile.gettempdir(), "datasensei_runner_audit.py")
    if not runner.exists() or runner.stat().st_mtime < RUNNER.stat().st_mtime:
        shutil.copy2(RUNNER, runner)
    environment = dict(os.environ, DS_WORKSPACE=workspace, DS_INPUT=workspace, DS_CPU_SECONDS="10")
    started = time.monotonic()
    try:
        done = subprocess.run([python, "-B", "-u", str(runner), str(Path(workspace, "practice.py"))],
                              stdin=subprocess.DEVNULL, capture_output=True, text=True, timeout=60, env=environment)
        code, error = done.returncode, done.stderr
    except subprocess.TimeoutExpired:
        code, error = 124, "timed out after 60 s"
    finally:
        shutil.rmtree(workspace, ignore_errors=True)
    last = ([line for line in error.strip().splitlines() if line.strip()] or [""])[-1]
    return {**block, "exit": code, "seconds": time.monotonic() - started, "error": last[:140]}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--python", default=sys.executable)
    parser.add_argument("--module", type=int)
    parser.add_argument("--jobs", type=int, default=2)
    parser.add_argument("--slow", type=float, default=8.0, help="report examples whose wall time exceeds this (use --jobs 1 for clean timings)")
    options = parser.parse_args()

    blocks = list(lesson_blocks(options.module))
    with ThreadPoolExecutor(max(1, options.jobs)) as pool:
        results = list(pool.map(lambda b: run(options.python, b), blocks))

    failed = [r for r in results if r["exit"] != 0 and "EOFError" not in r["error"]]
    slow = [r for r in results if r["exit"] == 0 and r["seconds"] > options.slow]

    for title, rows in (("FAILED", failed), (f"SLOWER THAN {options.slow:g} s", slow)):
        print(f"\n{title}: {len(rows)}")
        for r in rows:
            print(f"  Module{r['module']}LessonsSeeder.php:{r['line']}  {r['label'][:60]}  [{r['seconds']:.1f}s] {r['error']}")

    print(f"\n{len(results)} runnable examples checked, {len(failed)} failed.")
    return 1 if failed else 0


if __name__ == "__main__":
    raise SystemExit(main())
