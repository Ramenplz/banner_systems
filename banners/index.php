<?php
// banners/index.php
require_once '../includes/header.php';
require_once '../includes/db.php';

// ดึงข้อมูลแบนเนอร์ทั้งหมด
$query = "SELECT * FROM banners ORDER BY priority DESC, created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-6 max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-500">จัดการแบนเนอร์</h1>
  <a href="add.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full">เพิ่มแบนเนอร์</a>
</div>

<div class="white rounded-lg shadow overflow-hidden max-w-4xl mx-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ภาพตัวอย่าง</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อแบนเนอร์</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลำดับความสำคัญ</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <?php foreach ($banners as $banner): ?>
        <tr>
          <td class="px-6 py-4">
            <?php if (!empty($banner['image_url'])): ?>
              <img src="<?= htmlspecialchars($banner['image_url']) ?>"
                class="h-16 w-24 object-cover rounded border"
                alt="ตัวอย่างแบนเนอร์"
                onerror="this.style.display='none'">
            <?php else: ?>
              <span class="text-xs text-gray-500">ไม่มีภาพตัวอย่าง</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($banner['title']) ?></div>
            <div class="text-xs text-gray-500 truncate max-w-xs"><?= htmlspecialchars($banner['description']) ?></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500"><?= $banner['priority'] ?></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <?php
            $status = $banner['is_active'] ? 'ใช้งาน' : 'ปิดใช้งาน';
            $color = $banner['is_active'] ? 'green' : 'red';
            ?>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
              <?= $status ?>
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a href="edit.php?id=<?= $banner['banner_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">แก้ไข</a>
            <a href="delete.php?id=<?= $banner['banner_id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบแบนเนอร์นี้?')">ลบ</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>