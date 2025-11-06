<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

require_once __DIR__ . '/db_connect.php';


$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];

    // หาคิวถัดไป (หมายเลขน้อยที่สุดที่ยัง 'waiting')
    $sql_find_next = "SELECT id, ticket_number FROM tickets WHERE category = ? AND status = 'waiting' ORDER BY ticket_number ASC LIMIT 1";
    $stmt_find = $conn->prepare($sql_find_next);
    $stmt_find->bind_param("s", $category);
    $stmt_find->execute();
    $result = $stmt_find->get_result();
    
    if ($result->num_rows > 0) {
        $ticket_to_call = $result->fetch_assoc();
        $ticket_id = $ticket_to_call['id'];
        $ticket_number = $ticket_to_call['ticket_number'];

        $sql_update = "UPDATE tickets SET status = 'called' WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $ticket_id);

        if ($stmt_update->execute()) {
            $response = [
                'success' => true, 
                'called_number' => $ticket_number
            ];
        } else {
            $response['message'] = 'Failed to update ticket status.';
        }
        $stmt_update->close();
    } else {
        $response['message'] = 'No waiting tickets in this category.';
    }
    $stmt_find->close();
}

$conn->close();
echo json_encode($response);
?>