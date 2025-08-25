<?php
// includes/file_upload.php// includes/file_upload.php
function handle_file_upload($field_name, $subdirectory = 'banners')
{
  require_once __DIR__ . '/config.php';

  $upload_dir = UPLOAD_PATH . $subdirectory . '/';

  // สร้างโฟลเดอร์หากไม่มี
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }

  $file = $_FILES[$field_name];
  $result = [
    'success' => false,
    'file_path' => '', // จะเก็บ path เต็มสำหรับเก็บใน database
    'file_url' => '',  // จะเก็บ URL สำหรับแสดงผล
    'error' => ''
  ];

  // ตรวจสอบข้อผิดพลาดพื้นฐาน
  if ($file['error'] !== UPLOAD_ERR_OK) {
    $result['error'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ (Code: {$file['error']})";
    return $result;
  }

  // ตรวจสอบประเภทไฟล์
  $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  $file_type = mime_content_type($file['tmp_name']);
  if (!in_array($file_type, $allowed_types)) {
    $result['error'] = "อนุญาตเฉพาะไฟล์ภาพ: JPEG, PNG, GIF, WEBP";
    return $result;
  }

  // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
  $max_size = 5 * 1024 * 1024;
  if ($file['size'] > $max_size) {
    $result['error'] = "ขนาดไฟล์ต้องไม่เกิน 5MB";
    return $result;
  }

  // สร้างชื่อไฟล์ที่ไม่ซ้ำ
  $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  $filename = uniqid() . '_' . time() . '.' . $file_ext;
  $server_path = $upload_dir . $filename;

  // ย้ายไฟล์
  if (move_uploaded_file($file['tmp_name'], $server_path)) {
    $result['success'] = true;
    $result['file_path'] = 'uploads/' . $subdirectory . '/' . $filename; // เก็บแบบ relative path
    $result['file_url'] = UPLOAD_URL . $subdirectory . '/' . $filename; // URL เต็มสำหรับแสดงผล
  }

  return $result;
}
