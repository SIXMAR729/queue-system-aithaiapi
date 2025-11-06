<?php
session_start(); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าจอแสดงผลและเรียกคิว</title>
    <link rel="stylesheet" href="css/display.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <header>
            <h1>หน้าจอเรียกคิวสำหรับเจ้าหน้าที่</h1>
            <button id="reset-btn" class="reset-button">รีเซ็ตคิวทั้งหมด (เริ่มวันใหม่)</button>
            <button id="logout-staff" class="reset-button">ล๊อคเอ้าท์</button>
        </header>

        <div class="display-grid">
            <div class="card red" data-category="red">
                <h2>สีแดง / RED (ช่อง 6)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-red">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-red">-</strong></span>
                    <span>รออยู่: <strong id="waiting-red">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>

            <div class="card pink" data-category="pink">
                <h2>ชมพู / PINK (ช่อง 6)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-pink">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-pink">-</strong></span>
                    <span>รออยู่: <strong id="waiting-pink">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>

            <div class="card gray" data-category="gray">
                <h2>เทา / GRAY (ช่อง 6)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-gray">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-gray">-</strong></span>
                    <span>รออยู่: <strong id="waiting-gray">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>
            
            <div class="card green" data-category="green">
                <h2>เขียว / GREEN (ช่อง 7)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-green">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-green">-</strong></span>
                    <span>รออยู่: <strong id="waiting-green">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>

            <div class="card orange" data-category="orange">
                <h2>ส้ม / ORANGE (ช่อง 7)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-orange">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-orange">-</strong></span>
                    <span>รออยู่: <strong id="waiting-orange">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>

            <div class="card blue" data-category="blue">
                <h2>ฟ้า / BLUE (ช่อง 8)</h2>
                <div class="number-display">
                    <p>กำลังเรียก</p>
                    <h3 id="current-blue">0</h3>
                </div>
                <div class="queue-info">
                    <span>คิวถัดไป: <strong id="next-blue">-</strong></span>
                    <span>รออยู่: <strong id="waiting-blue">0</strong> คิว</span>
                </div>
                <button class="call-btn">เรียกคิวถัดไป</button>
            </div>

        </div>
    </div>
    <script src="js/display.js"></script>
</body>
</html>