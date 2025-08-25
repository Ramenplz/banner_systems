<?php
// banners/add.php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize_input($_POST['title']);
  $description = sanitize_input($_POST['description']);
  $target_url = sanitize_input($_POST['target_url']);
  $priority = (int)$_POST['priority'];
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  // รับเฉพาะ URL ภาพ
  $image_url = '';

  if (!empty($_POST['image_url'])) {
    // Use provided URL
    $image_type = 'external';
    $image_url = sanitize_input($_POST['image_url']);
    $image_path = null; // ตั้งค่าเป็น NULL สำหรับกรณีใช้ URL ภายนอก
  } else {
    $_SESSION['error'] = "กรุณาระบุ URL รูปภาพ";
    redirect('add.php');
  }

  $query = "INSERT INTO banners (title, description, image_type, image_path, image_url, target_url, priority, is_active) 
          VALUES (:title, :description, :image_type, :image_path, :image_url, :target_url, :priority, :is_active)";

  $stmt = $db->prepare($query);
  $stmt->bindParam(':title', $title);
  $stmt->bindParam(':description', $description);
  $stmt->bindParam(':image_type', $image_type);
  $stmt->bindParam(':image_path', $image_path);
  $stmt->bindParam(':image_url', $image_url);
  $stmt->bindParam(':target_url', $target_url);
  $stmt->bindParam(':priority', $priority);
  $stmt->bindParam(':is_active', $is_active);

  if ($stmt->execute()) {
    $_SESSION['success'] = "เพิ่มแบนเนอร์เรียบร้อยแล้ว";
    redirect('index.php');
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มแบนเนอร์";
  }
}
?>

<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-400 mb-6">เพิ่มแบนเนอร์ใหม่</h1>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="title">ชื่อแบนเนอร์</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="title" name="title" type="text" placeholder="ชื่อแบนเนอร์" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="description">คำอธิบาย</label>
      <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="description" name="description" rows="3" placeholder="คำอธิบายแบนเนอร์"></textarea>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="image_url">ระบุ URL รูปภาพ</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="image_url" name="image_url" type="url" placeholder="https://example.com/image.jpg" required>

      <div class="preview-container mt-4">
        <p class="text-gray-700 text-sm font-bold mb-2">ตัวอย่างภาพ:</p>
        <img id="image_preview" src="#" alt="ตัวอย่างภาพ" class="max-w-full h-auto max-h-48 border rounded hidden">
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="target_url">URL ปลายทาง</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="target_url" name="target_url" type="url" placeholder="https://example.com/landing-page">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="priority">ลำดับความสำคัญ (ตัวเลขมากแสดงก่อน)</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="priority" name="priority" type="number" value="0" min="0">
    </div>

    <div class="mb-4">
      <label class="inline-flex items-center">
        <input type="checkbox" class="form-checkbox" name="is_active" checked>
        <span class="ml-2">เปิดใช้งานแบนเนอร์</span>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
        บันทึกแบนเนอร์
      </button>
      <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        ยกเลิก
      </a>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // ระบบแสดงตัวอย่างภาพ
    const imageUrl = document.getElementById('image_url');
    const imagePreview = document.getElementById('image_preview');

    // ฟังก์ชันอัปเดตตัวอย่างภาพ
    function updatePreview(src) {
      if (src) {
        imagePreview.src = src;
        imagePreview.classList.remove('hidden');

        // กรณีรูปจาก URL โหลดไม่ได้
        imagePreview.onerror = function() {
          imagePreview.classList.add('hidden');
          alert('ไม่สามารถโหลดภาพจาก URL ที่ระบุได้');
          imageUrl.value = '';
        };
      } else {
        imagePreview.classList.add('hidden');
      }
    }

    // เมื่อป้อน URL
    imageUrl.addEventListener('input', function(e) {
      if (this.value) {
        updatePreview(this.value);
      } else {
        updatePreview(null);
      }
    });
  });
</script>