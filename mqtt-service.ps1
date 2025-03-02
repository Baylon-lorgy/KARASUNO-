# MQTT Service Script
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$logFile = Join-Path $scriptPath "mqtt-service.log"

function Write-Log {
    param($Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $Message" | Out-File -FilePath $logFile -Append
}

try {
    Write-Log "Starting MQTT Service..."
    Set-Location $scriptPath
    
    while ($true) {
        try {
            Write-Log "Starting MQTT subscriber..."
            & php artisan mqtt:subscribe
        } catch {
            Write-Log "Error occurred: $_"
            Write-Log "Restarting in 5 seconds..."
            Start-Sleep -Seconds 5
        }
    }
} catch {
    Write-Log "Critical error: $_"
    exit 1
} 