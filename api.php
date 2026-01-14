<?php
// api.php - VERSI TANPA LOGGING
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'smart_stick_db';

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// HAPUS BARIS INI: file_put_contents('api_debug.log', ...);

// Check if JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    // Try form data as fallback
    $data = $_POST;
    // HAPUS BARIS INI: file_put_contents('api_debug.log', ...);
}

// Validate required data
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received', 'raw_input' => $input]);
    exit();
}

// Extract data with fallbacks
$device_key = isset($data['device_key']) ? $data['device_key'] : '';
$distance1 = isset($data['distance1']) ? intval($data['distance1']) : 0;
$distance2 = isset($data['distance2']) ? intval($data['distance2']) : 0;
$soilWet = isset($data['soilWet']) ? intval($data['soilWet']) : 0;
$systemON = isset($data['systemON']) ? intval($data['systemON']) : 0;

// Validate device key (optional - bisa dihapus jika tidak diperlukan)
if (empty($device_key)) {
    // HAPUS BARIS INI: file_put_contents('api_debug.log', ...);
}

// Validate data ranges
if ($distance1 < 0 || $distance1 > 999) $distance1 = 999;
if ($distance2 < 0 || $distance2 > 999) $distance2 = 999;
$soilWet = ($soilWet == 1) ? 1 : 0;
$systemON = ($systemON == 1) ? 1 : 0;

// HAPUS BARIS INI: file_put_contents('api_debug.log', ...);

// Insert data into sensor_data table
$sql = "INSERT INTO sensor_data (distance1, distance2, soilWet, systemON) 
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    // HAPUS BARIS INI: file_put_contents('api_debug.log', ...);
    exit();
}

$stmt->bind_param("iiii", $distance1, $distance2, $soilWet, $systemON);

if ($stmt->execute()) {
    // Insert into logs (keep last 10)
    $sql_log = "INSERT INTO sensor_logs (distance1, distance2, soilWet, systemON) 
                VALUES (?, ?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    if ($stmt_log) {
        $stmt_log->bind_param("iiii", $distance1, $distance2, $soilWet, $systemON);
        $stmt_log->execute();
        $stmt_log->close();
        
        // Keep only last 10 logs
        $conn->query("DELETE FROM sensor_logs WHERE id NOT IN (
            SELECT id FROM (SELECT id FROM sensor_logs ORDER BY created_at DESC LIMIT 10) AS temp
        )");
    }
    
    $response = [
        'status' => 'success',
        'message' => 'Data inserted successfully',
        'data' => [
            'distance1' => $distance1,
            'distance2' => $distance2,
            'soilWet' => $soilWet,
            'systemON' => $systemON,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    // HAPUS BARIS INI: file_put_contents('api_debug.log', ...);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    // HAPUS BARIS INI: file_put_contents('api_debug.log', ...);
}

$stmt->close();
$conn->close();
?>