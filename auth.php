<?php
session_start(); // เริ่มต้นการใช้งาน session
require 'db_connect.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ค้นหาผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM staff WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // ตรวจสอบรหัสผ่านที่ถูกเข้ารหัส
        if (password_verify($password, $user['password'])) {
            // ถ้ารหัสผ่านถูกต้อง
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['id'] = $user['id'];
            session_regenerate_id(true);
            
            $response = ['success' => true];
        }
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>