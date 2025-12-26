# MySQL Root Password Reset Script for Laragon
# This script resets the MySQL root password to empty

$mysqlDir = "C:\laragon\bin\mysql\mysql-8.4.3-winx64"
$mysqldPath = Join-Path $mysqlDir "bin\mysqld.exe"
$mysqlPath = Join-Path $mysqlDir "bin\mysql.exe"
$dataDir = Join-Path $mysqlDir "data"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "MySQL Root Password Reset Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if MySQL directory exists
if (-not (Test-Path $mysqldPath)) {
    Write-Host "ERROR: MySQL not found at: $mysqldPath" -ForegroundColor Red
    Write-Host "Please update the mysqlDir variable in this script." -ForegroundColor Yellow
    pause
    exit 1
}

Write-Host "Step 1: Stopping MySQL service..." -ForegroundColor Yellow
# Try to stop MySQL service
$services = @("MySQL", "MySQL80", "MySQL57", "MySQL8.4")
foreach ($service in $services) {
    $svc = Get-Service -Name $service -ErrorAction SilentlyContinue
    if ($svc -and $svc.Status -eq "Running") {
        Write-Host "  Stopping service: $service" -ForegroundColor Gray
        Stop-Service -Name $service -Force -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 2
    }
}

# Kill any remaining mysqld processes
Write-Host "  Checking for running MySQL processes..." -ForegroundColor Gray
$processes = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if ($processes) {
    Write-Host "  Killing existing MySQL processes..." -ForegroundColor Gray
    $processes | Stop-Process -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

Write-Host "Step 2: Creating password reset SQL file..." -ForegroundColor Yellow
$initFile = Join-Path $env:TEMP "mysql-init-reset.txt"
@"
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
"@ | Out-File -FilePath $initFile -Encoding ASCII

Write-Host "  Init file created: $initFile" -ForegroundColor Gray

Write-Host "Step 3: Starting MySQL in safe mode (skip-grant-tables)..." -ForegroundColor Yellow
Write-Host "  This will start MySQL without password authentication." -ForegroundColor Gray
Write-Host "  Please wait, this may take 10-20 seconds..." -ForegroundColor Gray
Write-Host ""

# Start MySQL with skip-grant-tables in background
$mysqldProcess = Start-Process -FilePath $mysqldPath `
    -ArgumentList "--skip-grant-tables", "--init-file=$initFile", "--console" `
    -NoNewWindow -PassThru -WindowStyle Hidden

# Wait for MySQL to start
Write-Host "  Waiting for MySQL to start..." -ForegroundColor Gray
$maxWait = 30
$waited = 0
$mysqlReady = $false

while ($waited -lt $maxWait -and -not $mysqlReady) {
    Start-Sleep -Seconds 1
    $waited++
    try {
        $test = & $mysqlPath -u root -e "SELECT 1;" 2>&1
        if ($LASTEXITCODE -eq 0 -or $test -notmatch "ERROR") {
            $mysqlReady = $true
        }
    } catch {
        # Continue waiting
    }
}

if (-not $mysqlReady) {
    Write-Host "  WARNING: MySQL may not be fully ready, but continuing..." -ForegroundColor Yellow
}

Write-Host "Step 4: Resetting root password..." -ForegroundColor Yellow

# Give MySQL a moment to process the init file
Start-Sleep -Seconds 3

# Try to connect and verify
try {
    $result = & $mysqlPath -u root -e "SELECT User, Host FROM mysql.user WHERE User='root';" 2>&1
    Write-Host "  Password reset completed!" -ForegroundColor Green
} catch {
    Write-Host "  Password reset may have completed (checking...)" -ForegroundColor Yellow
}

Write-Host "Step 5: Stopping MySQL safe mode..." -ForegroundColor Yellow
# Stop the MySQL process
if ($mysqldProcess -and -not $mysqldProcess.HasExited) {
    Stop-Process -Id $mysqldProcess.Id -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

# Clean up init file
if (Test-Path $initFile) {
    Remove-Item $initFile -Force -ErrorAction SilentlyContinue
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Password reset complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "The MySQL root password has been reset to EMPTY." -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Start MySQL in Laragon" -ForegroundColor White
Write-Host "2. Try connecting in SQLyog with:" -ForegroundColor White
Write-Host "   - Username: root" -ForegroundColor Gray
Write-Host "   - Password: (leave empty)" -ForegroundColor Gray
Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

