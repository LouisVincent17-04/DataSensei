param(
    [string]$OutputPath = ""
)

$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $projectRoot "DataSensei-defense-clean.zip"
} elseif (-not [System.IO.Path]::IsPathRooted($OutputPath)) {
    $OutputPath = Join-Path $projectRoot $OutputPath
}

if (Test-Path -LiteralPath $OutputPath) {
    throw "Refusing to overwrite an existing archive: $OutputPath"
}

$temporaryRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("datasensei-defense-" + [guid]::NewGuid().ToString("N"))
$stage = Join-Path $temporaryRoot "DataSensei"
New-Item -ItemType Directory -Path $stage -Force | Out-Null

function Copy-ProjectFile {
    param([string]$RelativePath)

    $source = Join-Path $projectRoot $RelativePath
    if (-not (Test-Path -LiteralPath $source -PathType Leaf)) {
        return
    }

    $destination = Join-Path $stage $RelativePath
    $parent = Split-Path -Parent $destination
    New-Item -ItemType Directory -Path $parent -Force | Out-Null
    Copy-Item -LiteralPath $source -Destination $destination
}

try {
    # Explicit root allow-list: secrets and old audit/debug files are not copied.
    @(
        ".editorconfig",
        ".env.example",
        ".gitattributes",
        ".gitignore",
        "artisan",
        "composer.json",
        "composer.lock",
        "package.json",
        "package-lock.json",
        "phpunit.xml",
        "README.md",
        "vite.config.js",
        "start-datasensei.bat",
        "start-default-worker.bat",
        "start-ml-worker.bat",
        "start-scheduler.bat",
        "start-defense.bat"
    ) | ForEach-Object { Copy-ProjectFile $_ }

    # Application source folders. Generated dependencies/build output are
    # intentionally absent from this list and are rebuilt on the target PC.
    @(
        "app", "bootstrap", "config", "database", "deploy", "docker", "docs",
        "public", "resources", "routes", "scripts", "tests"
    ) | ForEach-Object {
        $root = Join-Path $projectRoot $_
        if (-not (Test-Path -LiteralPath $root -PathType Container)) {
            return
        }

        Get-ChildItem -LiteralPath $root -Recurse -File -Force | ForEach-Object {
            $relative = $_.FullName.Substring($projectRoot.Length) -replace '^[\\/]+', ''
            $normalized = $relative.Replace("\", "/")

            if ($normalized -like "public/build/*" -or
                $normalized -eq "public/hot" -or
                $normalized -eq "public/toput" -or
                $normalized -like "public/storage/*" -or
                ($normalized -like "bootstrap/cache/*" -and $normalized -ne "bootstrap/cache/.gitignore")) {
                return
            }

            Copy-ProjectFile $relative
        }
    }

    # Only immutable, bundled ML assets are distributable. Student uploads,
    # IDE workspaces, sandbox databases, logs, sessions, and cached views never
    # enter the archive.
    $systemMlRoot = Join-Path $projectRoot "storage/app/ml/system"
    if (Test-Path -LiteralPath $systemMlRoot -PathType Container) {
        Get-ChildItem -LiteralPath $systemMlRoot -Recurse -File -Force | ForEach-Object {
            $relative = $_.FullName.Substring($projectRoot.Length) -replace '^[\\/]+', ''
            Copy-ProjectFile $relative
        }
    }

    Get-ChildItem -LiteralPath (Join-Path $projectRoot "storage") -Recurse -File -Filter ".gitignore" -ErrorAction SilentlyContinue |
        Where-Object {
            $relative = $_.FullName.Substring($projectRoot.Length).Replace("\", "/")
            $relative -notlike "*/workspaces/*" -and
            $relative -notlike "*/sandbox/*" -and
            $relative -notlike "*/ml/users/*"
        } | ForEach-Object {
            $relative = $_.FullName.Substring($projectRoot.Length) -replace '^[\\/]+', ''
            Copy-ProjectFile $relative
        }

    Compress-Archive -Path (Join-Path $stage "*") -DestinationPath $OutputPath -CompressionLevel Optimal
    Write-Host "Created clean defense package: $OutputPath"
    Write-Host "Excluded: .env, Git history, dependencies, build output, user workspaces, sandbox databases, uploads, sessions, cache, and logs."
}
finally {
    if (Test-Path -LiteralPath $temporaryRoot) {
        Remove-Item -LiteralPath $temporaryRoot -Recurse -Force
    }
}
