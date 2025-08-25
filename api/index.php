<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

// ตรวจสอบ API Key ก่อนดำเนินการใดๆ
if (!isset($_SERVER['HTTP_X_API_KEY'])) {
  http_response_code(401);
  echo json_encode(['error' => 'API Key is required']);
  exit();
}

$api_key = $_SERVER['HTTP_X_API_KEY'];

// ตรวจสอบแพลตฟอร์ม
try {
    $stmt = $db->prepare("SELECT platform_id FROM platforms WHERE api_key = ? AND is_active = 1");
    $stmt->execute([$api_key]);
    $platform = $stmt->fetch();

    if (!$platform) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid API Key or platform is inactive']);
        exit();
    }

    // ดึงแบนเนอร์ที่ใช้งานได้สำหรับแพลตฟอร์มนี้
    $query = "SELECT 
                b.banner_id, 
                b.title, 
                b.description, 
                CASE 
                    WHEN b.image_type = 'upload' THEN CONCAT(:base_url, b.image_path)
                    ELSE COALESCE(bp.custom_image_url, b.image_url)
                END AS image_url,
                COALESCE(bp.custom_target_url, b.target_url) as target_url,
                bp.display_order
              FROM banner_platforms bp
              JOIN banners b ON bp.banner_id = b.banner_id
              WHERE bp.platform_id = :platform_id 
              AND bp.is_active = 1
              AND b.is_active = 1
              ORDER BY b.priority DESC, bp.display_order ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':platform_id' => $platform['platform_id'],
        ':base_url' => BASE_URL
    ]);
    
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งข้อมูลตอบกลับ
    echo json_encode([
        'success' => true,
        'data' => $banners,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
    exit();
}