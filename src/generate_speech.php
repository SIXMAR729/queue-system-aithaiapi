<?php
// เริ่ม session เพื่อตรวจสอบการล็อกอิน
session_start();
header('Content-Type: application/json');

// --- การรักษาความปลอดภัย: ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่ ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required!.']);
    exit;
}

// 
require_once __DIR__ . '/db_connect.php';

// 2. [FIXED] อ่านข้อมูลจาก $_POST (ไม่ใช่ php://input)
if (!isset($_POST['text']) || empty($_POST['text'])) {
    echo json_encode(['success' => false, 'message' => 'Text is required.']);
    exit;
}
$text_to_speak = $_POST['text'];

// --- ส่วนที่ 1: เรียก API เพื่อสังเคราะห์เสียง ---
require_once __DIR__ . '/config.php';

// 3. [FIXED] ใช้ API Key จากไฟล์ config ที่ปลอดภัย
$apiKey = API_KEY;

if (empty($apiKey)) {
    echo json_encode(['success' => false, 'message' => 'API key is not configured on the server.']);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.aiforthai.in.th/vaja',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode([
        "text" => $text_to_speak,
        "speaker" => "nana"
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

// 4. [FIXED] สร้างชื่อไฟล์และ path (path ของคุณถูกต้อง)
$filename = 'audio_' . time() . '.wav';
$filepath = __DIR__ . '/../public/audio/' . $filename;

// --- ส่วนที่ 3: [FIXED] บันทึกไฟล์และส่งคำตอบกลับ ---

// 5. บันทึกข้อมูลเสียงลงในไฟล์
file_put_contents($filepath, $audio_data);

// 6. ส่ง URL ที่ถูกต้องกลับไปให้ JavaScript
echo json_encode([
    'success' => true, 
    'file' => 'audio/' . $filename
]);

?>