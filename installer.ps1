# InstallScript.ps1

# Define URL and Installation Path
$scriptUrl = "https://geohopper.net/geohopper.ps1"
$installPath = "C:\Program Files\Geohopper"
$scriptPath = "$installPath\geohopper.ps1"

# Create Installation Directory
New-Item -ItemType Directory -Force -Path $installPath

# Download Geohopper Script
Invoke-WebRequest -Uri $scriptUrl -OutFile $scriptPath

# Add Installation Path to System PATH
$envPath = [System.Environment]::GetEnvironmentVariable("PATH", [System.EnvironmentVariableTarget]::Machine)
if (-not $envPath.Split(';').Contains($installPath)) {
    [System.Environment]::SetEnvironmentVariable("PATH", "$envPath;$installPath", [System.EnvironmentVariableTarget]::Machine)
}

# Create Wrapper Command
$wrapperPath = "$installPath\geohopper.cmd"
@"
@echo off
powershell -NoProfile -ExecutionPolicy Bypass -File "$scriptPath" %*
"@ | Out-File -FilePath $wrapperPath -Encoding ASCII
