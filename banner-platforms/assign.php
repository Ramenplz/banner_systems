<?php
// banner-platforms/assign.php
require_once '../includes/header.php';
require_once '../includes/db.php';

// ดึงข้อมูลแบนเนอร์และแพลตฟอร์ม
$banners_query = "SELECT banner_id, title, image_url FROM banners ORDER BY title ASC";
$platforms_query = "SELECT platform_id, platform_name FROM platforms ORDER BY platform_name ASC";

$banners = $db->query($banners_query)->fetchAll(PDO::FETCH_ASSOC);
$platforms = $db->query($platforms_query)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $banner_id = (int)$_POST['banner_id'];
  $platform_id = (int)$_POST['platform_id'];
  $custom_image_url = !empty($_POST['custom_image_url']) ? sanitize_input($_POST['custom_image_url']) : null;
  $custom_target_url = !empty($_POST['custom_target_url']) ? sanitize_input($_POST['custom_target_url']) : null;
  $display_order = (int)$_POST['display_order'];
  $is_active = isset($_POST['is_active']) ? 1 : 0;
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  // ตรวจสอบว่ามีการกำหนดแล้วหรือไม่
  $check_query = "SELECT id FROM banner_platforms WHERE banner_id = :banner_id AND platform_id = :platform_id";
  $check_stmt = $db->prepare($check_query);
  $check_stmt->bindParam(':banner_id', $banner_id, PDO::PARAM_INT);
  $check_stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
  $check_stmt->execute();

  if ($check_stmt->rowCount() > 0) {
    $_SESSION['error'] = "แบนเนอร์นี้ถูกกำหนดให้แพลตฟอร์มนี้แล้ว";
  } else {
    $insert_query = "INSERT INTO banner_platforms 
                    (banner_id, platform_id, custom_image_url, custom_target_url, 
                     display_order, is_active, start_date, end_date) 
                    VALUES 
                    (:banner_id, :platform_id, :custom_image_url, :custom_target_url, 
                     :display_order, :is_active, :start_date, :end_date)";

    $stmt = $db->prepare($insert_query);
    $stmt->bindParam(':banner_id', $banner_id, PDO::PARAM_INT);
    $stmt->bindParam(':platform_id', $platform_id, PDO::PARAM_INT);
    $stmt->bindParam(':custom_image_url', $custom_image_url);
    $stmt->bindParam(':custom_target_url', $custom_target_url);
    $stmt->bindParam(':display_order', $display_order, PDO::PARAM_INT);
    $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);

    if ($stmt->execute()) {
      $_SESSION['success'] = "กำหนดแบนเนอร์ให้แพลตฟอร์มเรียบร้อยแล้ว";
      redirect('manage.php');
    } else {
      $_SESSION['error'] = "เกิดข้อผิดพลาดในการกำหนดแบนเนอร์";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>กำหนดแบนเนอร์ให้แพลตฟอร์ม</title>
  <style>
    input[type="date"] {
      position: relative;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: auto;
      height: auto;
      color: transparent;
      background: transparent;
    }

    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-clear-button {
      display: none;
    }

    input[type="date"] {
      padding-right: 2.5rem;
      background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
      background-repeat: no-repeat;
      background-position: right .7rem top 50%;
      background-size: .65em auto;
    }

    input[type="datetime-local"] {
      padding-right: 0.75rem;
      background-image: none;
    }

    /* สไตล์สำหรับ datetime-local ที่มีไอคอนและใช้งานได้ปกติ */
    input[type="datetime-local"] {
      padding-right: 2.5rem;
      background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
      background-repeat: no-repeat;
      background-position: right .7rem top 50%;
      background-size: .65em auto;
    }

    /* เปิดใช้งาน calendar picker */
    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: auto;
      height: auto;
      color: transparent;
      background: transparent;
      opacity: 0;
    }

    .date-input-group {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .date-input-group input {
      flex: 1;
      position: relative;
    }
  </style>
</head>

<body>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-2xl text-blue-500 mb-6">กำหนดแบนเนอร์ให้แพลตฟอร์ม</h1>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="banner_id">แบนเนอร์</label>
        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          id="banner_id" name="banner_id" required>
          <option value="">-- เลือกแบนเนอร์ --</option>
          <?php foreach ($banners as $banner): ?>
            <option value="<?= $banner['banner_id'] ?>"
              data-image="<?= htmlspecialchars($banner['image_url']) ?>">
              <?= htmlspecialchars($banner['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <!-- เพิ่มส่วนแสดงตัวอย่างภาพแบนเนอร์ -->
        <div class="banner-preview-container mt-4 hidden">
          <p class="text-gray-700 text-sm font-bold mb-2">ตัวอย่างแบนเนอร์:</p>
          <div class="flex items-center">
            <img id="banner_preview" src="#" alt="ตัวอย่างแบนเนอร์" class="max-w-full h-auto max-h-32 border rounded mr-4">
            <span id="banner_title" class="text-gray-700"></span>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="platform_id">แพลตฟอร์ม</label>
        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          id="platform_id" name="platform_id" required>
          <option value="">-- เลือกแพลตฟอร์ม --</option>
          <?php foreach ($platforms as $platform): ?>
            <option value="<?= $platform['platform_id'] ?>"><?= htmlspecialchars($platform['platform_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">ระยะเวลาแสดงผล</label>
        <div class="date-input-group">
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="start_date" name="start_date" type="datetime-local"
            placeholder="วัน/เดือน/ปี เวลา" required
            value="<?= isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : '' ?>">
          <span class="flex items-center">ถึง</span>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="end_date" name="end_date" type="datetime-local"
            placeholder="วัน/เดือน/ปี เวลา" required
            value="<?= isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : '' ?>">
        </div>
      </div>
      
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="display_order">ลำดับการแสดงผล</label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          id="display_order" name="display_order" type="number" value="0" min="0">
        <p class="text-xs text-gray-500 mt-1">ตัวเลขน้อยแสดงก่อน</p>
      </div>

      <div class="mb-4">
        <label class="inline-flex items-center">
          <input type="checkbox" class="form-checkbox" name="is_active" checked>
          <span class="ml-2">เปิดใช้งานการแสดงผล</span>
        </label>
      </div>

      <div class="flex items-center justify-between">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-full focus:outline-none focus:shadow-outline" type="submit">
          บันทึกการกำหนด
        </button>
        <a href="manage.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-4 rounded-full focus:outline-none focus:shadow-outline">
          ดูรายการทั้งหมด
        </a>
      </div>
    </form>
  </div>

  <script>
    // แทนที่ส่วน JavaScript ที่จัดการวันที่ด้วยโค้ดนี้
    document.addEventListener('DOMContentLoaded', function() {
      // ตรวจสอบว่าวันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น
      const startDateInput = document.getElementById('start_date');
      const endDateInput = document.getElementById('end_date');

      startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
      });

      // ตั้งค่าขั้นต่ำของ endDate ถ้า startDate มีค่าอยู่แล้ว
      if (startDateInput.value) {
        endDateInput.min = startDateInput.value;
      }

      // ระบบแสดงตัวอย่างแบนเนอร์เมื่อเลือก
      const bannerSelect = document.getElementById('banner_id');
      const bannerPreviewContainer = document.querySelector('.banner-preview-container');
      const bannerPreview = document.getElementById('banner_preview');
      const bannerTitle = document.getElementById('banner_title');

      if (bannerSelect) {
        bannerSelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const imageUrl = selectedOption.getAttribute('data-image');

          if (this.value && imageUrl) {
            bannerPreview.src = imageUrl;
            bannerTitle.textContent = selectedOption.text;
            bannerPreviewContainer.classList.remove('hidden');

            bannerPreview.onerror = function() {
              bannerPreviewContainer.classList.add('hidden');
            };
          } else {
            bannerPreviewContainer.classList.add('hidden');
          }
        });

        if (bannerSelect.value) {
          bannerSelect.dispatchEvent(new Event('change'));
        }
      }
    });

    // เพิ่มสไตล์สำหรับ dropdown
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
      select.style.backgroundImage = "url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')";
      select.style.backgroundRepeat = "no-repeat";
      select.style.backgroundPosition = "right .7rem top 50%";
      select.style.backgroundSize = ".65em auto";
      select.style.paddingRight = "2.5rem";
    });

    document.addEventListener('DOMContentLoaded', function() {
      // ระบบแสดงตัวอย่างแบนเนอร์เมื่อเลือก
      const bannerSelect = document.getElementById('banner_id');
      const bannerPreviewContainer = document.querySelector('.banner-preview-container');
      const bannerPreview = document.getElementById('banner_preview');
      const bannerTitle = document.getElementById('banner_title');

      bannerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const imageUrl = selectedOption.getAttribute('data-image');

        if (this.value && imageUrl) {
          bannerPreview.src = imageUrl;
          bannerTitle.textContent = selectedOption.text;
          bannerPreviewContainer.classList.remove('hidden');

          // ตรวจสอบว่าภาพโหลดได้หรือไม่
          bannerPreview.onerror = function() {
            bannerPreviewContainer.classList.add('hidden');
          };
        } else {
          bannerPreviewContainer.classList.add('hidden');
        }
      });

      // ระบบแสดงตัวอย่างภาพที่กำหนดเอง
      const customImageUrl = document.getElementById('custom_image_url');
      const customImagePreviewContainer = document.querySelector('.custom-image-preview-container');
      const customImagePreview = document.getElementById('custom_image_preview');

      customImageUrl.addEventListener('input', function() {
        if (this.value) {
          customImagePreview.src = this.value;
          customImagePreviewContainer.classList.remove('hidden');

          // ตรวจสอบว่าภาพโหลดได้หรือไม่
          customImagePreview.onerror = function() {
            customImagePreviewContainer.classList.add('hidden');
            alert('ไม่สามารถโหลดภาพจาก URL ที่ระบุได้');
          };
        } else {
          customImagePreviewContainer.classList.add('hidden');
        }
      });

      // แสดงตัวอย่างแบนเนอร์ทันทีถ้ามีการเลือกแล้ว (กรณีกลับมาแก้ไข)
      if (bannerSelect.value) {
        bannerSelect.dispatchEvent(new Event('change'));
      }
    });
  </script>
</body>

</html>