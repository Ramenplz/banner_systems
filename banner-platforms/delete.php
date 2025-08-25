<?php
// banner-platforms/delete.php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage.php");
    exit();
}

$assignment_id = (int)$_GET['id'];

// ตรวจสอบว่ามีการกำหนดนี้จริงหรือไม่
$query = "SELECT id FROM banner_platforms WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $assignment_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = "ไม่พบการกำหนดนี้";
    header("Location: manage.php");
    exit();
}

// ลบการกำหนด
$query = "DELETE FROM banner_platforms WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $assignment_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "ลบการกำหนดแบนเนอร์เรียบร้อยแล้ว";
} else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบการกำหนด";
}

header("Location: manage.php");
exit();
?>