<?php
// platforms/add.php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $platform_name = sanitize_input($_POST['platform_name']);
  $platform_description = sanitize_input($_POST['platform_description']);
  $base_url = sanitize_input($_POST['base_url']);
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  // รับเฉพาะ URL โลโก้
  $logo_url = null;

  if (!empty($_POST['logo_url'])) {
    $logo_url = sanitize_input($_POST['logo_url']);
  }

  // สร้าง API Key อัตโนมัติ
  $api_key = bin2hex(random_bytes(16));

  $query = "INSERT INTO platforms 
              (platform_name, platform_description, api_key, base_url, logo_url, is_active) 
              VALUES 
              (:platform_name, :platform_description, :api_key, :base_url, :logo_url, :is_active)";

  $stmt = $db->prepare($query);
  $stmt->bindParam(':platform_name', $platform_name);
  $stmt->bindParam(':platform_description', $platform_description);
  $stmt->bindParam(':api_key', $api_key);
  $stmt->bindParam(':base_url', $base_url);
  $stmt->bindParam(':logo_url', $logo_url);
  $stmt->bindParam(':is_active', $is_active);

  if ($stmt->execute()) {
    $_SESSION['success'] = "เพิ่มแพลตฟอร์มเรียบร้อยแล้ว";
    redirect('index.php');
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มแพลตฟอร์ม";
  }
}
?>

<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-500 mb-6">เพิ่มแพลตฟอร์มใหม่</h1>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="platform_name">ชื่อแพลตฟอร์ม</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="platform_name" name="platform_name" type="text" placeholder="ชื่อแพลตฟอร์ม" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="platform_description">คำอธิบาย</label>
      <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="platform_description" name="platform_description" rows="3" placeholder="คำอธิบายแพลตฟอร์ม"></textarea>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="logo_url">URL โลโก้แพลตฟอร์ม</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="logo_url" name="logo_url" type="url" placeholder="https://example.com/logo.png">

      <!-- ส่วนแสดงตัวอย่างโลโก้ -->
      <div class="logo-preview-container mt-4">
        <p class="text-gray-700 text-sm font-bold mb-2">ตัวอย่างโลโก้:</p>
        <img id="logo_preview" src="#" alt="ตัวอย่างโลโก้" class="max-w-full h-auto max-h-32 border rounded hidden">
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="base_url">URL ฐาน</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="base_url" name="base_url" type="url" placeholder="https://example.com">
    </div>

    <div class="mb-4">
      <label class="inline-flex items-center">
        <input type="checkbox" class="form-checkbox" name="is_active" checked>
        <span class="ml-2">เปิดใช้งานแพลตฟอร์ม</span>
      </label>
    </div>

    <div class="flex items-center justify-between mt-5">
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-full focus:outline-none focus:shadow-outline" type="submit">
        บันทึกแพลตฟอร์ม
      </button>
      <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-4 rounded-full focus:outline-none focus:shadow-outline">
        ยกเลิก
      </a>
    </div>
  </form>
</div>

<script>
  // เพิ่มสคริปต์สำหรับแสดงตัวอย่างโลโก้
  document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logo_url');
    const logoPreview = document.getElementById('logo_preview');

    logoInput.addEventListener('input', function(e) {
      if (this.value) {
        logoPreview.src = this.value;
        logoPreview.classList.remove('hidden');

        // กรณีรูปจาก URL โหลดไม่ได้
        logoPreview.onerror = function() {
          logoPreview.classList.add('hidden');
          alert('ไม่สามารถโหลดภาพจาก URL ที่ระบุได้');
        };
      } else {
        logoPreview.classList.add('hidden');
      }
    });
  });
</script>