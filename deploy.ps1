# PowerShell deployment script for Hostinger (Windows)
# This script handles untracked files that conflict with Git pull

Write-Host "Starting deployment..." -ForegroundColor Green

# Navigate to project directory
Set-Location $PSScriptRoot

# Check if core/logger.php exists and is untracked
if (Test-Path "core/logger.php") {
    $isTracked = git ls-files --error-unmatch core/logger.php 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Removing untracked core/logger.php to prevent merge conflict..." -ForegroundColor Yellow
        Remove-Item -Force "core/logger.php"
    }
}

# Pull latest changes
Write-Host "Pulling latest changes from repository..." -ForegroundColor Green
git pull origin main

# If pull fails, try to reset and pull again
if ($LASTEXITCODE -ne 0) {
    Write-Host "Pull failed, attempting to resolve conflicts..." -ForegroundColor Yellow
    git fetch origin
    git reset --hard origin/main
}

Write-Host "Deployment completed!" -ForegroundColor Green

