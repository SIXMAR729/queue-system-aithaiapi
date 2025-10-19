<?php
require 'db_connect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json'); // บอกให้ client รู้ว่าเราจะส่งข้อมูลแบบ JSON กลับไป

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];

    // --- ส่วนของการ Transaction เพื่อป้องกันการซ้ำซ้อนของเลขคิว ---
    $conn->begin_transaction();

    try {
        // 1. หาหมายเลขคิวล่าสุดของประเภทนี้ โดยทำการ LOCK แถวไว้ชั่วคราว
        $sql_last_ticket = "SELECT MAX(ticket_number) as max_num FROM tickets WHERE category = ? FOR UPDATE";
        $stmt_last = $conn->prepare($sql_last_ticket);
        $stmt_last->bind_param("s", $category);
        $stmt_last->execute();
        $result = $stmt_last->get_result();
        $row = $result->fetch_assoc();

        $new_ticket_number = ($row['max_num']) ? $row['max_num'] + 1 : 1;
        $stmt_last->close();

        // 2. เพิ่มข้อมูลคิวใหม่ลงในฐานข้อมูล
        $sql_insert = "INSERT INTO tickets (category, ticket_number) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("si", $category, $new_ticket_number);

        if ($stmt_insert->execute()) {
            // ถ้าสำเร็จ ให้ commit การเปลี่ยนแปลง
            $conn->commit();
            $response = [
                'success' => true,
                'category' => $category,
                'ticket_number' => $new_ticket_number
            ];
        } else {
            // ถ้าไม่สำเร็จ ให้ rollback
            $conn->rollback();
            $response['message'] = 'Error: ' . $stmt_insert->error;
        }
        $stmt_insert->close();

    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Transaction Failed: ' . $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>