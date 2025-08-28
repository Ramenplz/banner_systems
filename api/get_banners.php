<?php
// api/get_banners.php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// กำหนดโดเมนที่อนุญาต
$allowed_origins = [
  'https://ramenplz.github.io',
  'https://ramenplz.github.io/' // เพิ่มรูปแบบนี้
];

// ตรวจสอบ origin ของ request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowed_origins)) {
  header("Access-Control-Allow-Origin: $origin");
} else {
  header("Access-Control-Allow-Origin: https://ramenplz.github.io");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// ตรวจสอบว่าเป็น request method ที่อนุญาต
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit(0);
}

// ตรวจสอบ API Key (รับได้ทั้งจาก header และ query parameter)
$api_key = null;
if (isset($_SERVER['HTTP_X_API_KEY'])) {
  $api_key = $_SERVER['HTTP_X_API_KEY'];
} elseif (isset($_GET['api_key'])) {
  $api_key = $_GET['api_key'];
}

if (!$api_key) {
  http_response_code(401);
  echo json_encode([
    'success' => false,
    'error' => 'API Key จำเป็นต้องใช้',
    'timestamp' => date('Y-m-d H:i:s')
  ]);
  exit;
}

// ตรวจสอบว่า API Key ถูกต้อง
$query = "SELECT platform_id FROM platforms WHERE api_key = :api_key AND is_active = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':api_key', $api_key);
$stmt->execute();
$platform = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$platform) {
  http_response_code(403);
  echo json_encode([
    'success' => false,
    'error' => 'API Key ไม่ถูกต้องหรือแพลตฟอร์มถูกปิดใช้งาน',
    'timestamp' => date('Y-m-d H:i:s')
  ]);
  exit;
}

$platform_id = $platform['platform_id'];

// ดึงแบนเนอร์สำหรับแพลตฟอร์มนี้
$query = "SELECT 
            b.banner_id,
            b.title,
            b.description,
            CASE 
                WHEN b.image_type = 'upload' THEN CONCAT('" . BASE_URL . "', b.image_path)
                ELSE COALESCE(bp.custom_image_url, b.image_url)
            END AS image_url,
            COALESCE(bp.custom_target_url, b.target_url) AS target_url
          FROM banner_platforms bp
          JOIN banners b ON bp.banner_id = b.banner_id
          WHERE bp.platform_id = :platform_id
          AND bp.is_active = 1
          AND b.is_active = 1";

$stmt = $db->prepare($query);
$stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ส่งข้อมูลกลับ
echo json_encode([
  'success' => true,
  'data' => $banners,
  'timestamp' => date('Y-m-d H:i:s'),
  'count' => count($banners)
]);
