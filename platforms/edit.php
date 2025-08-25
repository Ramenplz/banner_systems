<?php
// platforms/edit.php
require_once '../includes/header.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
  redirect('index.php');
}

$platform_id = (int)$_GET['id'];

// ดึงข้อมูลแพลตฟอร์ม
$query = "SELECT * FROM platforms WHERE platform_id = :platform_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
$stmt->execute();
$platform = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$platform) {
  $_SESSION['error'] = "ไม่พบแพลตฟอร์มนี้";
  redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $platform_name = sanitize_input($_POST['platform_name']);
  $platform_description = sanitize_input($_POST['platform_description']);
  $base_url = sanitize_input($_POST['base_url']);
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  // รับเฉพาะ URL โลโก้
  $logo_url = $platform['logo_url'];

  if (!empty($_POST['logo_url'])) {
    $logo_url = sanitize_input($_POST['logo_url']);
  }

  $query = "UPDATE platforms SET 
              platform_name = :platform_name, 
              platform_description = :platform_description, 
              base_url = :base_url, 
              logo_url = :logo_url,
              is_active = :is_active
              WHERE platform_id = :platform_id";
              
  $stmt = $db->prepare($query);
  $stmt->bindParam(':platform_name', $platform_name);
  $stmt->bindParam(':platform_description', $platform_description);
  $stmt->bindParam(':base_url', $base_url);
  $stmt->bindParam(':logo_url', $logo_url);
  $stmt->bindParam(':is_active', $is_active);
  $stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
    $_SESSION['success'] = "อัปเดตแพลตฟอร์มเรียบร้อยแล้ว";
    redirect('index.php');
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตแพลตฟอร์ม";
  }
}
?>

<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-400 mb-6">แก้ไขแพลตฟอร์ม</h1>

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
        id="platform_name" name="platform_name" type="text" placeholder="ชื่อแพลตฟอร์ม"
        value="<?= htmlspecialchars($platform['platform_name']) ?>" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="platform_description">คำอธิบาย</label>
      <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="platform_description" name="platform_description" rows="3"
        placeholder="คำอธิบายแพลตฟอร์ม"><?= htmlspecialchars($platform['platform_description']) ?></textarea>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="logo_url">URL โลโก้แพลตฟอร์ม</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="logo_url" name="logo_url" type="url" placeholder="https://example.com/logo.png"
        value="<?= htmlspecialchars($platform['logo_url']) ?>">

      <!-- แสดงโลโก้ปัจจุบันถ้ามี -->
      <?php if (!empty($platform['logo_url'])): ?>
        <div class="mt-4">
          <p class="text-gray-700 text-sm font-bold mb-2">โลโก้ปัจจุบัน:</p>
          <img src="<?= htmlspecialchars($platform['logo_url']) ?>" alt="โลโก้แพลตฟอร์ม" class="max-w-full h-auto max-h-32 border rounded" onerror="this.style.display='none'">
        </div>
      <?php endif; ?>

      <!-- ส่วนแสดงตัวอย่างโลโก้ใหม่ -->
      <div class="logo-preview-container mt-4">
        <p class="text-gray-700 text-sm font-bold mb-2">ตัวอย่างโลโก้ใหม่:</p>
        <img id="logo_preview" src="#" alt="ตัวอย่างโลโก้ใหม่" class="max-w-full h-auto max-h-32 border rounded hidden">
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="base_url">URL ฐาน</label>
      <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        id="base_url" name="base_url" type="url" placeholder="https://example.com"
        value="<?= htmlspecialchars($platform['base_url']) ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="api_key">API Key</label>
      <div class="flex items-center">
        <input class="shadow appearance-none border rounded w-10/12 py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
          id="api_key" type="text" value="<?= htmlspecialchars($platform['api_key']) ?>" readonly>
        <button type="button" onclick="copyApiKey()" class="ml-2 w-2/12 border border-gray-400 bg-white hover:bg-gray-500 hover:text-white text-gray-500 font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline">
          คัดลอก
        </button>
      </div>
      <p class="text-xs text-gray-500 mt-1">ใช้คีย์นี้สำหรับการเชื่อมต่อ API</p>
    </div>

    <div class="mb-4">
      <label class="inline-flex items-center">
        <input type="checkbox" class="form-checkbox" name="is_active" <?= $platform['is_active'] ? 'checked' : '' ?>>
        <span class="ml-2">เปิดใช้งานแพลตฟอร์ม</span>
      </label>
    </div>

    <div class="flex items-center justify-between">
      <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
        อัปเดตแพลตฟอร์ม
      </button>
      <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-4 rounded focus:outline-none focus:shadow-outline">
        ยกเลิก
      </a>
    </div>
  </form>
</div>

<script>
  function copyApiKey() {
    const apiKey = document.getElementById('api_key');
    apiKey.select();
    document.execCommand('copy');
    alert('คัดลอก API Key เรียบร้อยแล้ว');
  }

  // เพิ่มสคริปต์สำหรับแสดงตัวอย่างโลโก้จาก URL
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