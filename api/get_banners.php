<?php
// api/get_banners.php
require_once '../includes/config.php';
require_once '../includes/db.php';

// ===== CORS =====
// ถ้าอยากให้เฉพาะ github pages ของคุณเข้าถึง ให้ใช้บรรทัดนี้
header("Access-Control-Allow-Origin: https://ramenplz.github.io");
// ถ้าอยากให้ทุกที่เข้าถึง ให้ใช้ * แทน
// header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

// Preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ===== ตรวจสอบ API Key =====
if (!isset($_SERVER['HTTP_X_API_KEY'])) {
    http_response_code(401);
    echo json_encode(['error' => 'API Key จำเป็นต้องใช้']);
    exit;
}

$api_key = $_SERVER['HTTP_X_API_KEY'];

// ตรวจสอบว่า API Key ถูกต้อง
$query = "SELECT platform_id FROM platforms WHERE api_key = :api_key AND is_active = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':api_key', $api_key);
$stmt->execute();
$platform = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$platform) {
    http_response_code(403);
    echo json_encode(['error' => 'API Key ไม่ถูกต้องหรือแพลตฟอร์มถูกปิดใช้งาน']);
    exit;
}

$platform_id = $platform['platform_id'];

// ===== ดึงแบนเนอร์สำหรับแพลตฟอร์มนี้ =====
$query = "SELECT 
            b.banner_id,
            b.title,
            b.description,
            COALESCE(bp.custom_image_url, b.image_url) AS image_url,
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

// ===== ส่งข้อมูลกลับ =====
echo json_encode([
    'success' => true,
    'data' => $banners,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
