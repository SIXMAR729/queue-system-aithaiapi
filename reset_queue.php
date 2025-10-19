<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

require 'db_connect.php';

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