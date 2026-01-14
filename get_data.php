<?php
// get_data.php - API untuk dashboard mendapatkan data sensor

require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = getConnection();

// Jika koneksi gagal, return data dummy
if (!$conn) {
    echo json_encode([
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'device' => [
            'name' => 'ESP8266-NodeMCU',
            'last_seen' => date('Y-m-d H:i:s')
        ],
        'current' => [
            'distance1' => 35.50,
            'distance2' => 15.20,
            'soilWet' => false,
            'systemON' => true,
            'created_at' => date('Y-m-d H:i:s')
        ],
        'history' => []
    ]);
    exit();
}

// Get current sensor data
$currentQuery = "SELECT * FROM sensor_data ORDER BY created_at DESC LIMIT 1";
$currentResult = $conn->query($currentQuery);

if (!$currentResult) {
    // Jika tabel tidak ada, setup otomatis
    $conn->close();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database not setup. Run setup.php first.'
    ]);
    exit();
}

$currentData = $currentResult->fetch_assoc();

// Jika tidak ada data, buat data default
if (!$currentData) {
    $currentData = [
        'distance1' => 0,
        'distance2' => 0,
        'soilWet' => 0,
        'systemON' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Get last 10 logs
$historyQuery = "SELECT * FROM sensor_data ORDER BY created_at DESC LIMIT 10";
$historyResult = $conn->query($historyQuery);
$historyData = [];

if ($historyResult) {
    while ($row = $historyResult->fetch_assoc()) {
        $historyData[] = [
            'distance1' => intval($row['distance1']),
            'distance2' => intval($row['distance2']),
            'soilWet' => boolval($row['soilWet']),
            'systemON' => boolval($row['systemON']),
            'created_at' => $row['created_at']
        ];
    }
}

// Get device status
$deviceQuery = "SELECT device_name, last_seen FROM devices LIMIT 1";
$deviceResult = $conn->query($deviceQuery);
$deviceData = $deviceResult ? $deviceResult->fetch_assoc() : null;

// Close connection
$conn->close();

// Prepare response
$response = [
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'device' => [
        'name' => $deviceData['device_name'] ?? 'ESP8266-NodeMCU',
        'last_seen' => $deviceData['last_seen'] ?? date('Y-m-d H:i:s')
    ],
    'current' => [
        'distance1' => intval($currentData['distance1']),
        'distance2' => intval($currentData['distance2']),
        'soilWet' => boolval($currentData['soilWet']),
        'systemON' => boolval($currentData['systemON']),
        'created_at' => $currentData['created_at']
    ],
    'history' => $historyData
];

echo json_encode($response);
?>