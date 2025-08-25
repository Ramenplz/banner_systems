<?php
// banners/delete.php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
  redirect('index.php');
}

$banner_id = (int)$_GET['id'];

// ตรวจสอบว่ามีแบนเนอร์นี้จริงหรือไม่
$query = "SELECT banner_id FROM banners WHERE banner_id = :banner_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':banner_id', $banner_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
  $_SESSION['error'] = "ไม่พบแบนเนอร์นี้";
  redirect('index.php');
}

// ลบแบนเนอร์
$query = "DELETE FROM banners WHERE banner_id = :banner_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':banner_id', $banner_id, PDO::PARAM_INT);

if ($stmt->execute()) {
  $_SESSION['success'] = "ลบแบนเนอร์เรียบร้อยแล้ว";
} else {
  $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบแบนเนอร์";
}

redirect('index.php');
