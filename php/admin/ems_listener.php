<?php
require_once __DIR__ . '/vendor/autoload.php';
use XBase\TableReader;

$api_key      = $_POST['api_key'] ?? null;
$file_name    = $_POST['file_name'] ?? null;
$base64_data  = $_POST['file_content'] ?? null;

if (!$api_key || !$base64_data) {
    http_response_code(400);
    echo "Missing critical data.";
    exit;
}

// 1. Convert the Base64 string back into the raw binary dBASE table layout
$binary_data = base64_decode($base64_data);

// 2. Save it as a temporary local file on your web server so XBase can stream it
$tmp_file = sys_get_temp_dir() . '/' . $file_name;
file_put_contents($tmp_file, $binary_data);

try {
    // 3. Let your fresh XBase parser read the data natively
    $table = new TableReader($tmp_file, ['encoding' => 'cp1252']);
    
    // Detect file type by extension
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if ($extension === 'veh') {
        while ($record = $table->nextRecord()) {
            $vin = $record->get('VIN');
            echo "Processing VIN: $vin\n";
            // Execute your MySQL update queries here...
        }
    }
    
    $table->close();
    unlink($tmp_file); // Clean up temp file
    
    echo "success"; // This lets PowerShell know it's safe to clear the local file
} catch (\Exception $e) {
    http_response_code(500);
    echo "Parser error: " . $e->getMessage();
}