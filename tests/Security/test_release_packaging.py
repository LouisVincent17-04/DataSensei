from __future__ import annotations

import importlib.util
import sys
import tempfile
import unittest
import zipfile
from pathlib import Path, PurePosixPath


PROJECT_ROOT = Path(__file__).resolve().parents[2]
PACKAGER_PATH = PROJECT_ROOT / "scripts/package-release.py"
SPEC = importlib.util.spec_from_file_location("datasensei_release_packager", PACKAGER_PATH)
if SPEC is None or SPEC.loader is None:
    raise RuntimeError("Unable to load the release packager.")
packager = importlib.util.module_from_spec(SPEC)
sys.modules[SPEC.name] = packager
SPEC.loader.exec_module(packager)


class ReleasePackagingTest(unittest.TestCase):
    def test_allow_list_excludes_every_private_runtime_path(self) -> None:
        paths = {
            entry.relative.as_posix()
            for entry in packager.collect_source_entries(PROJECT_ROOT)
        }

        self.assertIn(".env.example", paths)
        self.assertNotIn(".env", paths)
        self.assertFalse(any(path.startswith(".git/") for path in paths))
        self.assertFalse(any(path.startswith("node_modules/") for path in paths))
        self.assertFalse(any(path.startswith("vendor/") for path in paths))
        self.assertFalse(any(path.startswith("public/build/") for path in paths))
        self.assertFalse(any(path.startswith("public/storage/") for path in paths))
        self.assertTrue(packager.is_forbidden(PurePosixPath("public/storage")))
        self.assertFalse(any(path.startswith("storage/logs/") for path in paths))
        self.assertFalse(any(path.startswith("storage/framework/sessions/") for path in paths))
        self.assertFalse(any(path.startswith("storage/app/workspaces/") for path in paths))
        self.assertFalse(any(path.startswith("storage/app/sandbox/") for path in paths))

    def test_non_placeholder_environment_secrets_are_rejected(self) -> None:
        findings = packager.secret_findings(
            PurePosixPath(".env.example"),
            b"APP_KEY=base64:not-a-real-key\nDB_PASSWORD=do-not-package-this\n",
        )

        self.assertTrue(any("APP_KEY" in finding for finding in findings))
        self.assertTrue(any("DB_PASSWORD" in finding for finding in findings))

    def test_expected_public_storage_link_is_skipped_without_being_followed(self) -> None:
        with tempfile.TemporaryDirectory(prefix="datasensei-storage-link-test-") as directory:
            root = Path(directory)
            (root / "app").mkdir()
            (root / "app/example.php").write_text("<?php\n", encoding="utf-8")
            (root / "public").mkdir()
            target = root / "storage/app/public"
            target.mkdir(parents=True)
            (target / "private-upload.txt").write_text("do not package", encoding="utf-8")

            try:
                (root / "public/storage").symlink_to(target, target_is_directory=True)
            except OSError as error:
                self.skipTest(f"Directory symlinks are unavailable: {error}")

            paths = {
                entry.relative.as_posix()
                for entry in packager.collect_source_entries(root)
            }

            self.assertIn("app/example.php", paths)
            self.assertFalse(any(path.startswith("public/storage") for path in paths))
            self.assertNotIn("storage/app/public/private-upload.txt", paths)

    def test_real_source_archive_builds_and_verifies_cleanly(self) -> None:
        with tempfile.TemporaryDirectory(prefix="datasensei-release-test-") as directory:
            output = Path(directory) / "DataSensei-clean.zip"
            packager.build_archive(output, PROJECT_ROOT)
            packager.verify_archive(output)

            with zipfile.ZipFile(output) as archive:
                names = set(archive.namelist())

            self.assertIn("DataSensei/RELEASE_MANIFEST.sha256", names)
            self.assertIn("DataSensei/.env.example", names)
            self.assertNotIn("DataSensei/.env", names)

            tampered = Path(directory) / "DataSensei-tampered.zip"
            with zipfile.ZipFile(output, "r") as source, zipfile.ZipFile(tampered, "w") as target:
                for member in source.infolist():
                    data = source.read(member)
                    if member.filename == "DataSensei/README.md":
                        data += b"\nunauthorized change\n"
                    target.writestr(member, data)

            with self.assertRaises(packager.ReleasePackagingError):
                packager.verify_archive(tampered)

    def test_deployable_archive_includes_only_built_frontend_output(self) -> None:
        manifest = PROJECT_ROOT / "public/build/manifest.json"
        if not manifest.is_file():
            self.skipTest("Run npm run build to exercise deployable-asset packaging.")

        with tempfile.TemporaryDirectory(prefix="datasensei-deployable-test-") as directory:
            output = Path(directory) / "DataSensei-deployable.zip"
            packager.build_archive(
                output,
                PROJECT_ROOT,
                include_built_assets=True,
            )

            with zipfile.ZipFile(output) as archive:
                names = set(archive.namelist())

            self.assertIn("DataSensei/public/build/manifest.json", names)
            self.assertFalse(any("/node_modules/" in name for name in names))


if __name__ == "__main__":
    unittest.main()
