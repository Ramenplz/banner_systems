<?php
// banner-platforms/manage.php
require_once '../includes/header.php';
require_once '../includes/db.php';

$query = "SELECT 
            bp.id, 
            b.title AS banner_title, 
            p.platform_name,
            p.logo_url AS platform_logo,
            bp.display_order,
            bp.is_active,
            b.is_active AS banner_active,
            bp.start_date,
            bp.end_date,       
            bp.custom_image_url,  
            b.image_path,         
            b.image_url,
            b.image_type           
          FROM 
            banner_platforms bp
          JOIN 
            banners b ON bp.banner_id = b.banner_id
          JOIN 
            platforms p ON bp.platform_id = p.platform_id
          ORDER BY 
            p.platform_name, bp.display_order";

$stmt = $db->prepare($query);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-6 max-w-4xl mx-auto">
  <h1 class="text-2xl text-blue-500">จัดการการกำหนดแสดงผล</h1>
  <a href="assign.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full">กำหนดการแสดงผลใหม่</a>
</div>

<div class="white rounded-lg shadow overflow-hidden max-w-4xl mx-auto">
  <table class=" min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แพลตฟอร์ม</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แบนเนอร์</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลำดับการแสดง</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ระยะเวลา</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 px-8 pt-6 pb-8 mb-4">
      <?php foreach ($assignments as $assignment):
        $is_active = $assignment['is_active'] && $assignment['banner_active'];
        $now = time();
        $start_date = strtotime($assignment['start_date']);
        $end_date = strtotime($assignment['end_date']);
        $is_within_date = ($now >= $start_date && $now <= $end_date);
      ?>
        <tr>

          <td class="px-6 py-4 flex items-start">
            <div class="flex items-center">
              <?php if (!empty($assignment['platform_logo'])): ?>
                <div class="flex-shrink-0 h-10 w-10 mr-3">
                  <img class="h-10 w-10 rounded-full object-cover"
                    src="<?= htmlspecialchars($assignment['platform_logo']) ?>"
                    alt="<?= htmlspecialchars($assignment['platform_name']) ?>"
                    onerror="this.style.display='none'">
                </div>
              <?php endif; ?>
              <div class="text-sm font-medium text-gray-900">
                <?= htmlspecialchars($assignment['platform_name']) ?>
              </div>
            </div>
          </td>

          <td class="px-6 py-4">
            <div class="text-sm text-gray-900">
              <?php if ($assignment['custom_image_url']): ?>
                <span class="text-xs text-blue-500">(ใช้ภาพกำหนดเอง)</span>
              <?php endif; ?>
            </div>
            <!-- ส่วนแสดงภาพตัวอย่างแบนเนอร์ -->
            <div class="mt-1">
              <?php
              $image_to_show = '';
              if (!empty($assignment['custom_image_url'])) {
                $image_to_show = $assignment['custom_image_url'];
              } elseif ($assignment['image_type'] == 'upload' && !empty($assignment['image_path'])) {
                $image_to_show = BASE_URL . $assignment['image_path'];
              } elseif (!empty($assignment['image_url'])) {
                $image_to_show = $assignment['image_url'];
              }

              if ($image_to_show): ?>
                <img src="<?= htmlspecialchars($image_to_show) ?>"
                  class="h-10 object-contain border rounded"
                  alt="ตัวอย่างภาพแบนเนอร์"
                  onerror="this.style.display='none'">
              <?php else: ?>
                <span class="text-xs text-gray-500">ไม่มีภาพตัวอย่าง</span>
              <?php endif; ?>
            </div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500"><?= $assignment['display_order'] ?></div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <?php if ($is_active && $is_within_date): ?>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                กำลังแสดงผล
              </span>
            <?php elseif (!$is_active): ?>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                ปิดใช้งาน
              </span>
            <?php else: ?>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                อยู่นอกระยะเวลา
              </span>
            <?php endif; ?>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500">
              <?= date('d/m/Y H:i', strtotime($assignment['start_date'])) ?> -
              <?= date('d/m/Y H:i', strtotime($assignment['end_date'])) ?>
            </div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a href="edit.php?id=<?= $assignment['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">แก้ไข</a>
            <a href="delete.php?id=<?= $assignment['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบการกำหนดนี้?')">ลบ</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>