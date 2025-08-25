<?php
// platforms/delete.php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
  redirect('index.php');
}

$platform_id = (int)$_GET['id'];

// ตรวจสอบว่ามีแพลตฟอร์มนี้จริงหรือไม่
$query = "SELECT platform_id FROM platforms WHERE platform_id = :platform_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
  $_SESSION['error'] = "ไม่พบแพลตฟอร์มนี้";
  redirect('index.php');
}

// ตรวจสอบว่ามีแบนเนอร์ที่ใช้งานแพลตฟอร์มนี้อยู่หรือไม่
$query = "SELECT COUNT(*) FROM banner_platforms WHERE platform_id = :platform_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count > 0) {
  $_SESSION['error'] = "ไม่สามารถลบแพลตฟอร์มนี้ได้ เนื่องจากมีแบนเนอร์ที่ใช้งานอยู่";
  redirect('index.php');
}

// ลบแพลตฟอร์ม
$query = "DELETE FROM platforms WHERE platform_id = :platform_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);

if ($stmt->execute()) {
  $_SESSION['success'] = "ลบแพลตฟอร์มเรียบร้อยแล้ว";
} else {
  $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบแพลตฟอร์ม";
}

redirect('index.php');
