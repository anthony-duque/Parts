# --- CONFIGURATION ---
$WatchFolder = "C:\CCC_EMS_Out\"
$ApiUrl      = "https://thepartsapp.info/php/admin/ems_listener.php"
$ApiKey      = "abc123xyz_unique_to_shop_location"

Write-Host "Monitoring folder: $WatchFolder for CCC ONE file changes..." -ForegroundColor Green

# 1. Initialize the native Windows File Watcher object
$Watcher = New-Object System.IO.FileSystemWatcher
$Watcher.Path = $WatchFolder
$Watcher.Filter = "*.*" # Monitors all files (.VEH, .LIN, .ENV, etc.)
$Watcher.IncludeSubdirectories = $false
$Watcher.EnableRaisingEvents = $true

# 2. Define the automation action when a file drops or updates
$Action = {
    param($sender, $eventArgs)
    
    $FilePath = $eventArgs.FullPath
    $FileName = $eventArgs.Name
    
    # Wait a fraction of a second to let CCC ONE finish writing the file to disk
    Start-Sleep -Milliseconds 300
    
    try {
        # Read the file content as raw bytes to handle the binary dBASE encoding safely
        # Converting it to standard Base64 ensures your web server receives it flawlessly
        $Bytes = [System.IO.File]::ReadAllBytes($FilePath)
        $Base64Content = [Convert]::ToBase64String($Bytes)
        
        # Build the structured payload for your online PHP backend
        $Body = @{
            api_key      = $ApiKey
            file_name    = $FileName
            file_content = $Base64Content
        }
        
        Write-Host "Sending updated file: $FileName to server..." -ForegroundColor Cyan
        
        # Native Windows HTTP POST Request
        $Response = Invoke-RestMethod -Uri $ApiUrl -Method Post -Body $Body
        
        # If the web server responds perfectly, delete the local copy to keep the folder clear
        if ($Response -like "*success*") {
            Remove-Item $FilePath -Force
            Write-Host "File $FileName uploaded and cleared successfully.`n" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "Error processing file: $_" -ForegroundColor Red
    }
}

# 3. Bind the script action to standard Windows file creation and changes
$CreatedEvent = Register-ObjectEvent $Watcher "Created" -Action $Action
$ChangedEvent = Register-ObjectEvent $Watcher "Changed" -Action $Action

# Keep the monitoring process running indefinitely
while ($true) { Start-Sleep -Seconds 5 }
