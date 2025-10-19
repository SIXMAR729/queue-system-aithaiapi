<?php

ini_set('session.cookie_httponly', 1); // ป้องกัน JavaScript ขโมย cookie
ini_set('session.cookie_secure', 1); // บังคับส่ง cookie ผ่าน HTTPS เท่านั้น (ต้องทำหลังติดตั้ง SSL แล้ว)

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // หรือ IP ของเซิร์ฟเวอร์ฐานข้อมูล
$username = "root";        // ชื่อผู้ใช้ฐานข้อมูลของคุณ
$password = "";            // รหัสผ่านฐานข้อมูลของคุณ
$dbname = "queue_db";      // ชื่อฐานข้อมูลที่คุณสร้าง

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตั้งค่า character set เป็น utf8
$conn->set_charset("utf8");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>