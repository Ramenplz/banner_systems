<?php
// platforms/index.php
require_once '../includes/header.php';
require_once '../includes/db.php';

// ดึงข้อมูลแพลตฟอร์มทั้งหมด
$query = "SELECT * FROM platforms ORDER BY platform_name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-6 max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-500">จัดการแพลตฟอร์ม</h1>
  <a href="add.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full">เพิ่มแพลตฟอร์ม</a>
</div>

<div class="white rounded-lg shadow overflow-hidden max-w-4xl mx-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แพลตฟอร์ม</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Key</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL ฐาน</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <?php foreach ($platforms as $platform): ?>
        <tr>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <?php if (!empty($platform['logo_url'])): ?>
                <div class="flex-shrink-0 h-10 w-10">
                  <img class="h-10 w-10 rounded-full object-cover" src="<?= htmlspecialchars($platform['logo_url']) ?>" alt="<?= htmlspecialchars($platform['platform_name']) ?>" onerror="this.style.display='none'">
                </div>
              <?php endif; ?>
              <div class="<?= !empty($platform['logo_url']) ? 'ml-4' : '' ?>">
                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($platform['platform_name']) ?></div>
                <div class="text-sm text-gray-500"><?= htmlspecialchars($platform['platform_description']) ?></div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500 font-mono"><?= substr($platform['api_key'], 0, 6) ?>******</div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500"><?= htmlspecialchars($platform['base_url']) ?></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <?php
            $status = $platform['is_active'] ? 'ใช้งาน' : 'ปิดใช้งาน';
            $color = $platform['is_active'] ? 'green' : 'red';
            ?>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
              <?= $status ?>
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a href="edit.php?id=<?= $platform['platform_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">แก้ไข</a>
            <a href="delete.php?id=<?= $platform['platform_id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบแพลตฟอร์มนี้?')">ลบ</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>