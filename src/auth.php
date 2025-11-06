<?php
session_start(); 
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM staff WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
         
        if (password_verify($password, $user['password'])) {
            
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