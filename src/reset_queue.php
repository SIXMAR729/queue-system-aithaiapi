<?php

session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    
    http_response_code(401); // 401 means "Unauthorized"
    echo json_encode([
        'success' => false, 
        'message' => 'Authentication required. You must be logged in to do this.'
    ]);
    exit; 
}


require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

// TRUNCATE TABLE จะเร็วกว่า DELETE และจะรีเซ็ต auto-increment ID กลับไปเป็น 1
$sql = "TRUNCATE TABLE tickets";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Queue has been reset.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error resetting queue: ' . $conn->error]);
}

$conn->close();
?>