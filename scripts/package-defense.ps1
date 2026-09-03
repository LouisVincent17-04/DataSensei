param(
    [string]$OutputPath = ""
)

$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$packager = Join-Path $PSScriptRoot "package-release.py"

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $projectRoot "DataSensei-defense-clean.zip"
} elseif (-not [System.IO.Path]::IsPathRooted($OutputPath)) {
    $OutputPath = Join-Path $projectRoot $OutputPath
}

if (Test-Path -LiteralPath $OutputPath) {
    throw "Refusing to overwrite an existing archive: $OutputPath"
}

$node = Get-Command "node" -ErrorAction SilentlyContinue
$npm = Get-Command "npm.cmd" -ErrorAction SilentlyContinue
if (-not $npm) {
    $npm = Get-Command "npm" -ErrorAction SilentlyContinue
}
if (-not $node -or -not $npm) {
    throw "Node.js 22.12.x and npm 10.x are required to build the frontend assets."
}

$nodeVersion = (& $node.Source --version).Trim()
$npmVersion = (& $npm.Source --version).Trim()
if ($nodeVersion -notmatch '^v22\.12\.\d+$') {
    throw "Expected Node.js 22.12.x from .nvmrc, but found $nodeVersion."
}
if ($npmVersion -notmatch '^10\.\d+\.\d+$') {
    throw "Expected npm 10.x, but found $npmVersion."
}

Push-Location $projectRoot
try {
    & $npm.Source ci
    if ($LASTEXITCODE -ne 0) {
        throw "npm ci failed. Install the pinned Node.js version from .nvmrc and retry."
    }

    & $npm.Source run build
    if ($LASTEXITCODE -ne 0) {
        throw "The Vite production build failed."
    }
} finally {
    Pop-Location
}

$pythonLauncher = Get-Command "py" -ErrorAction SilentlyContinue
if ($pythonLauncher) {
    & $pythonLauncher.Source -3 $packager --output $OutputPath --include-built-assets
} else {
    $pythonLauncher = Get-Command "python" -ErrorAction SilentlyContinue
    if (-not $pythonLauncher) {
        throw "Python 3 is required to create and verify a clean release archive."
    }

    & $pythonLauncher.Source $packager --output $OutputPath --include-built-assets
}

if ($LASTEXITCODE -ne 0) {
    throw "Release packaging failed. No archive was published."
}
