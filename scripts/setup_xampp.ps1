<#
setup_xampp.ps1
Usage (PowerShell as Administrator):
  .\setup_xampp.ps1 -ProjectPath "C:\path\to\WebDatSanPic"
If -ProjectPath is omitted, it uses the script's parent folder.
#>
param(
    [string]$ProjectPath = (Split-Path -Parent $MyInvocation.MyCommand.Definition)
)

function Fail([string]$msg){ Write-Host $msg -ForegroundColor Red; exit 1 }

$xampp = 'C:\xampp'
if (-not (Test-Path $xampp)) {
    Fail "XAMPP not found at $xampp. Please install XAMPP first: https://www.apachefriends.org/index.html"
}

$src = Resolve-Path -Path $ProjectPath
$dest = Join-Path $xampp 'htdocs\WebDatSanPic'

Write-Host "Copying project from $src to $dest"
if (Test-Path $dest) {
    Write-Host "Destination exists — backing up old folder to ${dest}.bak"
    Remove-Item -Recurse -Force "${dest}.bak" -ErrorAction SilentlyContinue
    Rename-Item -Path $dest -NewName "WebDatSanPic.bak" -ErrorAction SilentlyContinue
}

Copy-Item -Recurse -Force -Path (Join-Path $src '*') -Destination $dest

# Start XAMPP (uses the xampp_start.bat/exe shipped with XAMPP)
$startExe = Join-Path $xampp 'xampp_start.exe'
$startBat = Join-Path $xampp 'xampp_start.bat'
if (Test-Path $startExe) {
    Write-Host "Starting XAMPP via xampp_start.exe"
    Start-Process -FilePath $startExe -WindowStyle Hidden
} elseif (Test-Path $startBat) {
    Write-Host "Starting XAMPP via xampp_start.bat"
    Start-Process -FilePath $startBat -WindowStyle Hidden
} else {
    Write-Host "Could not find xampp_start script. Please start Apache and MySQL from XAMPP Control Panel." -ForegroundColor Yellow
}

Start-Sleep -Seconds 6

# Import database
$sqlPath = Join-Path $dest 'database\datlichthethao.sql'
$mysqlExe = Join-Path $xampp 'mysql\bin\mysql.exe'
if (-not (Test-Path $mysqlExe)) { Fail "MySQL client not found at $mysqlExe" }
if (-not (Test-Path $sqlPath)) { Fail "SQL file not found at $sqlPath" }

Write-Host "Creating database 'datlichthethao' if not exists..."
& $mysqlExe -u root -e "CREATE DATABASE IF NOT EXISTS datlichthethao CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" 2>&1

Write-Host "Importing SQL dump (this may take a while)"
# Use Get-Content piped to mysql to avoid Windows redirection issues
Get-Content -Path $sqlPath -Raw | & $mysqlExe -u root datlichthethao

if ($LASTEXITCODE -ne 0) {
    Write-Host "Import may have failed (exit code $LASTEXITCODE). Check MySQL logs or import manually via phpMyAdmin." -ForegroundColor Yellow
} else {
    Write-Host "Database import finished." -ForegroundColor Green
}

# Open browser
$indexUrl = 'http://localhost/WebDatSanPic/index.php'
Write-Host "Opening $indexUrl"
Start-Process $indexUrl

Write-Host "Done. If the site doesn't load, open XAMPP Control Panel and ensure Apache + MySQL are running." -ForegroundColor Cyan
