<?php
// เริ่ม session เพื่อตรวจสอบการล็อกอิน
session_start();
header('Content-Type: application/json');

// --- การรักษาความปลอดภัย: ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่ ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

// รับข้อความจาก POST request
$post_data = json_decode(file_get_contents('php://input'), true);
if (!isset($post_data['text']) || empty($post_data['text'])) {
    echo json_encode(['success' => false, 'message' => 'Text is required.']);
    exit;
}
$text_to_speak = $post_data['text'];

// --- ส่วนที่ 1: เรียก API เพื่อสังเคราะห์เสียง ---
$apiKey = ""; // 🚨 **สำคัญมาก: ใส่ API Key ของคุณที่นี่**

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.aiforthai.in.th/vaja',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30, // ตั้ง Timeout ไว้ 30 วินาที
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode([
      "text" => $text_to_speak,
      "speaker" => "nana" // สามารถเปลี่ยนเสียงผู้พูดได้ เช่น haru, sara, anan
  ]),
  CURLOPT_HTTPHEADER => array(
    "Apikey: " . $apiKey,
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['success' => false, 'message' => "cURL Error #: " . $err]);
    exit;
}

$response_json = json_decode($response, true);
if (!isset($response_json['audio_url'])) {
    echo json_encode(['success' => false, 'message' => 'Failed to get audio URL from API.', 'api_response' => $response_json]);
    exit;
}

$audio_url = $response_json['audio_url'];

// --- ส่วนที่ 2: ดาวน์โหลดไฟล์เสียง ---
$curl_download = curl_init();
curl_setopt_array($curl_download, array(
  CURLOPT_URL => $audio_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    "Apikey: " . $apiKey
  ),
));

$audio_data = curl_exec($curl_download);
$err_download = curl_error($curl_download);
curl_close($curl_download);

if ($err_download) {
    echo json_encode(['success' => false, 'message' => "cURL Download Error #: " . $err_download]);
    exit;
}

// สร้างชื่อไฟล์ที่ไม่ซ้ำกันเพื่อป้องกันการเขียนทับ
$filename = 'audio_' . time() . '.wav'; 
file_put_contents($filename, $audio_data);

// ส่งผลลัพธ์กลับไปให้ JavaScript
echo json_encode(['success' => true, 'audioUrl' => $filename]);

?>