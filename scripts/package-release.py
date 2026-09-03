#!/usr/bin/env python3
"""Create and verify a clean, source-only DataSensei release archive.

The archive is built from an explicit allow-list. Runtime state, local secrets,
dependencies, build output, user uploads, workspaces, logs, sessions, caches,
and database sandboxes are never candidates for inclusion. A high-confidence
secret scan runs before the temporary archive is promoted to the requested
output path.
"""

from __future__ import annotations

import argparse
import hashlib
import os
import re
import stat
import sys
import tempfile
import zipfile
from dataclasses import dataclass
from pathlib import Path, PurePosixPath
from typing import Iterable, Iterator


PROJECT_ROOT = Path(__file__).resolve().parents[1]
ARCHIVE_ROOT = "DataSensei"

ROOT_FILES = (
    ".editorconfig",
    ".env.example",
    ".gitattributes",
    ".gitignore",
    ".nvmrc",
    "artisan",
    "composer.json",
    "composer.lock",
    "HYBRID_ML_PACKAGE_MANIFEST.txt",
    "INSTALL_HYBRID_ML.txt",
    "package.json",
    "package-lock.json",
    "phpunit.xml",
    "README.md",
    "README_MODEL_DEVELOPMENT.md",
    "start-datasensei.bat",
    "start-default-worker.bat",
    "start-defense.bat",
    "start-ml-worker.bat",
    "start-scheduler.bat",
    "vite.config.js",
)

SOURCE_DIRECTORIES = (
    ".github",
    "app",
    "bootstrap",
    "config",
    "database",
    "deploy",
    "docker",
    "docs",
    "public",
    "resources",
    "routes",
    "scripts",
    "tests",
)

FORBIDDEN_PARTS = {
    ".git",
    ".idea",
    ".mypy_cache",
    ".nova",
    ".pytest_cache",
    ".vscode",
    ".zed",
    "__pycache__",
    "node_modules",
    "vendor",
}

FORBIDDEN_PREFIXES = (
    "bootstrap/cache/",
    "public/storage/",
    "storage/app/data-toolkit/workspaces/",
    "storage/app/ml/users/",
    "storage/app/private/",
    "storage/app/public/",
    "storage/app/sandbox/",
    "storage/app/workspaces/",
    "storage/framework/cache/",
    "storage/framework/sessions/",
    "storage/framework/views/",
    "storage/logs/",
)

FORBIDDEN_FILES = {
    "public/hot",
    "public/storage",
    "public/toput",
}

TEXT_SCAN_LIMIT = 16 * 1024 * 1024
PRIVATE_KEY_PATTERN = re.compile(
    rb"-----BEGIN (?:RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----"
)
KNOWN_TOKEN_PATTERNS = (
    ("AWS access key", re.compile(rb"\b(?:AKIA|ASIA)[A-Z0-9]{16}\b")),
    ("GitHub token", re.compile(rb"\bgh[pousr]_[A-Za-z0-9]{30,}\b")),
    ("Slack token", re.compile(rb"\bxox[baprs]-[A-Za-z0-9-]{20,}\b")),
    ("Stripe live key", re.compile(rb"\b(?:sk|rk)_live_[A-Za-z0-9]{16,}\b")),
)
SENSITIVE_ENV_KEYS = {
    "APP_KEY",
    "AWS_ACCESS_KEY_ID",
    "AWS_SECRET_ACCESS_KEY",
    "AWS_SESSION_TOKEN",
    "DB_PASSWORD",
    "MAIL_PASSWORD",
    "REDIS_PASSWORD",
}
SAFE_ENV_VALUES = {
    "",
    "null",
    "none",
    "example",
    "password",
    "change-me",
    "changeme",
    "your-password",
    "your_app_password",
}


class ReleasePackagingError(RuntimeError):
    """Raised when a release package is unsafe or cannot be verified."""


@dataclass(frozen=True)
class SourceEntry:
    source: Path
    relative: PurePosixPath


def normalized_relative(path: Path, project_root: Path = PROJECT_ROOT) -> PurePosixPath:
    return PurePosixPath(path.relative_to(project_root).as_posix())


def is_forbidden(relative: PurePosixPath) -> bool:
    value = relative.as_posix()
    parts = set(relative.parts)

    if parts & FORBIDDEN_PARTS:
        return True
    if relative.name == ".env" or (
        relative.name.startswith(".env.") and value != ".env.example"
    ):
        return True
    if value in FORBIDDEN_FILES:
        return True
    if any(value.startswith(prefix) for prefix in FORBIDDEN_PREFIXES):
        return True
    if value.startswith("bootstrap/cache/") and value != "bootstrap/cache/.gitignore":
        return True

    return False


def iter_directory_files(root: Path, project_root: Path = PROJECT_ROOT) -> Iterator[Path]:
    for path in sorted(root.rglob("*"), key=lambda item: item.as_posix().lower()):
        if path.is_symlink():
            if is_forbidden(normalized_relative(path, project_root)):
                continue
            raise ReleasePackagingError(
                f"Refusing to follow symlink while packaging: {path.relative_to(project_root)}"
            )
        if path.is_file():
            yield path


def collect_source_entries(
    project_root: Path = PROJECT_ROOT,
    *,
    include_built_assets: bool = False,
) -> list[SourceEntry]:
    entries: dict[str, SourceEntry] = {}

    def add_file(path: Path) -> None:
        relative = normalized_relative(path, project_root)
        if (
            relative.as_posix().startswith("public/build/")
            and not include_built_assets
        ):
            return
        if is_forbidden(relative):
            return
        entries[relative.as_posix()] = SourceEntry(path, relative)

    for relative_name in ROOT_FILES:
        path = project_root / relative_name
        if path.is_symlink():
            raise ReleasePackagingError(f"Refusing to package symlink: {relative_name}")
        if path.is_file():
            add_file(path)

    for directory_name in SOURCE_DIRECTORIES:
        directory = project_root / directory_name
        if not directory.exists():
            continue
        if not directory.is_dir() or directory.is_symlink():
            raise ReleasePackagingError(
                f"Expected a real source directory: {directory_name}"
            )
        for path in iter_directory_files(directory, project_root):
            add_file(path)

    # Include only immutable built-in ML reference assets from storage.
    system_ml = project_root / "storage/app/ml/system"
    if system_ml.is_dir() and not system_ml.is_symlink():
        for path in iter_directory_files(system_ml, project_root):
            add_file(path)

    # Preserve empty Laravel runtime directories through their safe placeholders.
    storage_root = project_root / "storage"
    if storage_root.is_dir():
        for path in iter_directory_files(storage_root, project_root):
            if path.name == ".gitignore":
                add_file(path)

    if not entries:
        raise ReleasePackagingError("The source allow-list produced an empty package.")

    return [entries[key] for key in sorted(entries)]


def _safe_env_value(value: str) -> bool:
    normalized = value.strip().strip('"\'').strip().lower()
    return (
        normalized in SAFE_ENV_VALUES
        or normalized.startswith("your-")
        or normalized.startswith("example-")
        or (normalized.startswith("${") and normalized.endswith("}"))
        or (normalized.startswith("<") and normalized.endswith(">"))
    )


def secret_findings(relative: PurePosixPath, data: bytes) -> list[str]:
    findings: list[str] = []
    if len(data) > TEXT_SCAN_LIMIT or b"\x00" in data[:8192]:
        return findings

    if PRIVATE_KEY_PATTERN.search(data):
        findings.append("private key material")

    for label, pattern in KNOWN_TOKEN_PATTERNS:
        if pattern.search(data):
            findings.append(label)

    text = data.decode("utf-8", errors="ignore")
    for line_number, line in enumerate(text.splitlines(), start=1):
        if "=" not in line or line.lstrip().startswith("#"):
            continue
        key, value = line.split("=", 1)
        key = key.strip()
        if key in SENSITIVE_ENV_KEYS and not _safe_env_value(value):
            findings.append(f"non-placeholder {key} at line {line_number}")

    return findings


def validate_entries(entries: Iterable[SourceEntry]) -> list[tuple[SourceEntry, bytes, str]]:
    validated: list[tuple[SourceEntry, bytes, str]] = []
    problems: list[str] = []

    for entry in entries:
        if is_forbidden(entry.relative):
            problems.append(f"forbidden path selected: {entry.relative}")
            continue

        data = entry.source.read_bytes()
        findings = secret_findings(entry.relative, data)
        if findings:
            problems.append(f"{entry.relative}: {', '.join(findings)}")
            continue

        validated.append((entry, data, hashlib.sha256(data).hexdigest()))

    if problems:
        formatted = "\n - ".join(problems)
        raise ReleasePackagingError(f"Release safety validation failed:\n - {formatted}")

    return validated


def zip_info(name: str, *, executable: bool = False) -> zipfile.ZipInfo:
    info = zipfile.ZipInfo(name, date_time=(2026, 1, 1, 0, 0, 0))
    info.compress_type = zipfile.ZIP_DEFLATED
    info.create_system = 3
    mode = 0o755 if executable else 0o644
    info.external_attr = (stat.S_IFREG | mode) << 16
    return info


def build_archive(
    output_path: Path,
    project_root: Path = PROJECT_ROOT,
    *,
    include_built_assets: bool = False,
) -> Path:
    output_path = output_path.resolve()
    if output_path.exists():
        raise ReleasePackagingError(f"Refusing to overwrite existing file: {output_path}")
    output_path.parent.mkdir(parents=True, exist_ok=True)

    entries = validate_entries(
        collect_source_entries(
            project_root,
            include_built_assets=include_built_assets,
        )
    )
    if include_built_assets and not any(
        entry.relative.as_posix() == "public/build/manifest.json"
        for entry, _, _ in entries
    ):
        raise ReleasePackagingError(
            "Built assets were requested, but public/build/manifest.json is missing. "
            "Run npm ci and npm run build first."
        )
    manifest_lines: list[str] = []

    fd, temporary_name = tempfile.mkstemp(
        prefix=f".{output_path.name}.", suffix=".tmp", dir=output_path.parent
    )
    os.close(fd)
    temporary_path = Path(temporary_name)

    try:
        with zipfile.ZipFile(
            temporary_path,
            mode="w",
            compression=zipfile.ZIP_DEFLATED,
            compresslevel=9,
        ) as archive:
            for entry, data, digest in entries:
                archive_name = f"{ARCHIVE_ROOT}/{entry.relative.as_posix()}"
                executable = entry.relative.as_posix() in {
                    "artisan",
                    "scripts/package-release.py",
                }
                archive.writestr(zip_info(archive_name, executable=executable), data)
                manifest_lines.append(f"{digest}  {entry.relative.as_posix()}")

            manifest_data = ("\n".join(manifest_lines) + "\n").encode("utf-8")
            archive.writestr(
                zip_info(f"{ARCHIVE_ROOT}/RELEASE_MANIFEST.sha256"), manifest_data
            )

        verify_archive(temporary_path)
        os.replace(temporary_path, output_path)
    except BaseException:
        temporary_path.unlink(missing_ok=True)
        raise

    return output_path


def verify_archive(archive_path: Path) -> None:
    problems: list[str] = []
    seen: set[str] = set()
    actual_hashes: dict[str, str] = {}

    with zipfile.ZipFile(archive_path, "r") as archive:
        bad_zip_member = archive.testzip()
        if bad_zip_member:
            problems.append(f"CRC failure: {bad_zip_member}")

        for member in archive.infolist():
            name = PurePosixPath(member.filename)
            if member.filename in seen:
                problems.append(f"duplicate member: {member.filename}")
            seen.add(member.filename)

            if name.is_absolute() or ".." in name.parts:
                problems.append(f"unsafe archive path: {member.filename}")
                continue
            if not name.parts or name.parts[0] != ARCHIVE_ROOT:
                problems.append(f"member outside {ARCHIVE_ROOT}/: {member.filename}")
                continue
            if member.is_dir():
                continue

            relative = PurePosixPath(*name.parts[1:])
            if is_forbidden(relative):
                problems.append(f"forbidden archive member: {relative}")

            data = archive.read(member)
            findings = secret_findings(relative, data)
            if findings:
                problems.append(f"{relative}: {', '.join(findings)}")

            if relative.as_posix() != "RELEASE_MANIFEST.sha256":
                actual_hashes[relative.as_posix()] = hashlib.sha256(data).hexdigest()

        manifest_name = f"{ARCHIVE_ROOT}/RELEASE_MANIFEST.sha256"
        if manifest_name not in seen:
            problems.append("missing RELEASE_MANIFEST.sha256")
        else:
            expected_hashes: dict[str, str] = {}
            manifest = archive.read(manifest_name).decode("utf-8", errors="strict")
            for line_number, line in enumerate(manifest.splitlines(), start=1):
                match = re.fullmatch(r"([0-9a-f]{64})  (.+)", line)
                if not match:
                    problems.append(f"malformed manifest line {line_number}")
                    continue
                digest, relative_name = match.groups()
                if relative_name in expected_hashes:
                    problems.append(f"duplicate manifest path: {relative_name}")
                    continue
                expected_hashes[relative_name] = digest

            missing_from_manifest = sorted(set(actual_hashes) - set(expected_hashes))
            missing_from_archive = sorted(set(expected_hashes) - set(actual_hashes))
            for relative_name in missing_from_manifest:
                problems.append(f"archive member missing from manifest: {relative_name}")
            for relative_name in missing_from_archive:
                problems.append(f"manifest member missing from archive: {relative_name}")
            for relative_name in sorted(set(actual_hashes) & set(expected_hashes)):
                if actual_hashes[relative_name] != expected_hashes[relative_name]:
                    problems.append(f"manifest hash mismatch: {relative_name}")

    if problems:
        formatted = "\n - ".join(problems)
        raise ReleasePackagingError(f"Archive verification failed:\n - {formatted}")


def parse_args(argv: list[str]) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--output",
        type=Path,
        help="Destination ZIP. Required unless --verify-only is used.",
    )
    parser.add_argument(
        "--verify-only",
        type=Path,
        metavar="ZIP",
        help="Verify an existing release archive without creating one.",
    )
    parser.add_argument(
        "--include-built-assets",
        action="store_true",
        help="Include the versioned public/build output after a successful Vite build.",
    )
    args = parser.parse_args(argv)

    if bool(args.output) == bool(args.verify_only):
        parser.error("provide exactly one of --output or --verify-only")

    return args


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv or sys.argv[1:])

    try:
        if args.verify_only:
            verify_archive(args.verify_only.resolve())
            print(f"Verified clean release archive: {args.verify_only.resolve()}")
        else:
            created = build_archive(
                args.output,
                include_built_assets=args.include_built_assets,
            )
            print(f"Created clean release archive: {created}")
            asset_summary = (
                "Included only versioned public/build assets; "
                if args.include_built_assets
                else "Excluded generated build output; "
            )
            print(
                asset_summary
                + "excluded local secrets, Git history, dependencies, uploads, workspaces, "
                "sandboxes, sessions, caches, and logs."
            )
    except (OSError, zipfile.BadZipFile, ReleasePackagingError) as error:
        print(f"ERROR: {error}", file=sys.stderr)
        return 1

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
