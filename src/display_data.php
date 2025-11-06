<?php
require_once __DIR__ . '/db_connect.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

header('Content-Type: application/json');

$categories = ['red', 'pink', 'gray', 'green', 'orange', 'blue'];
$queue_data = [];

foreach ($categories as $category) {
    // 1. หาคิวล่าสุดที่ถูกเรียก (status = 'called')
    $sql_current = "SELECT MAX(ticket_number) as current_ticket FROM tickets WHERE category = ? AND status = 'called'";
    $stmt_current = $conn->prepare($sql_current);
    $stmt_current->bind_param("s", $category);
    $stmt_current->execute();
    $result_current = $stmt_current->get_result()->fetch_assoc();
    $current_ticket = $result_current['current_ticket'] ? (int)$result_current['current_ticket'] : 0;
    $stmt_current->close();

    // 2. หาคิวถัดไปที่ต้องเรียก (status = 'waiting')
    $sql_next = "SELECT MIN(ticket_number) as next_ticket FROM tickets WHERE category = ? AND status = 'waiting'";
    $stmt_next = $conn->prepare($sql_next);
    $stmt_next->bind_param("s", $category);
    $stmt_next->execute();
    $result_next = $stmt_next->get_result()->fetch_assoc();
    $next_ticket = $result_next['next_ticket'] ? (int)$result_next['next_ticket'] : null;
    $stmt_next->close();

    // 3. นับจำนวนคิวที่เหลือ
    $sql_waiting_count = "SELECT COUNT(id) as waiting_count FROM tickets WHERE category = ? AND status = 'waiting'";
    $stmt_count = $conn->prepare($sql_waiting_count);
    $stmt_count->bind_param("s", $category);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result()->fetch_assoc();
    $waiting_count = (int)$result_count['waiting_count'];
    $stmt_count->close();
    
    $queue_data[$category] = [
        'current' => $current_ticket,
        'next' => $next_ticket,
        'waiting' => $waiting_count
    ];
}

$conn->close();
echo json_encode($queue_data);
?>