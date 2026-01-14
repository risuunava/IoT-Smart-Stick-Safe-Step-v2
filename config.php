<?php
// config.php - Database configuration

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Password kosong untuk XAMPP default
define('DB_NAME', 'smart_stick_db');

// Create connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Jangan exit, tapi return null dan handle di caller
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    
    return $conn;
}

// Device validation
function validateDevice($deviceKey) {
    $conn = getConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("SELECT device_name FROM devices WHERE device_key = ?");
    $stmt->bind_param("s", $deviceKey);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $isValid = $result->num_rows > 0;
    
    if ($isValid) {
        $updateStmt = $conn->prepare("UPDATE devices SET last_seen = CURRENT_TIMESTAMP WHERE device_key = ?");
        $updateStmt->bind_param("s", $deviceKey);
        $updateStmt->execute();
        $updateStmt->close();
    }
    
    $stmt->close();
    $conn->close();
    
    return $isValid;
}
?>