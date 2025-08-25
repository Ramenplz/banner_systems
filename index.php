<?php
// index.php
require_once 'includes/header.php';
require_once 'includes/db.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <a href="banners/" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-blue-500">แบนเนอร์</h3>
        <div class="mt-2 text-sm text-gray-500">
          จัดการภาพแบนเนอร์ทั้งหมดของคุณ
        </div>
      </div>
    </a>

    <a href="platforms/" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-blue-500">แพลตฟอร์ม</h3>
        <div class="mt-2 text-sm text-gray-500">
          กำหนดแพลตฟอร์มที่จะแสดงแบนเนอร์
        </div>
      </div>
    </a>

    <a href="banner-platforms/manage.php" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-blue-500">การกำหนดแสดงผล</h3>
        <div class="mt-2 text-sm text-gray-500">
          กำหนดว่าแบนเนอร์ใดแสดงบนแพลตฟอร์มใด
        </div>
      </div>
    </a>
  </div>


  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
      <a href="banner-platforms/manage.php" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-lg leading-6 font-medium text-blue-500">การกำหนดแสดงผลล่าสุด</h3>
      </a>
      <p class="mt-1 max-w-2xl text-sm text-gray-500">รายการกำหนดการแสดงแบนเนอร์บนแพลตฟอร์มต่างๆ</p>
    </div>
    <div class="border-t border-gray-200">
      <?php
      // ปรับปรุง query เพื่อดึงข้อมูลภาพ
      $query = "SELECT 
                  bp.id,
                  b.title AS banner_title,
                  b.image_url AS banner_image,
                  p.platform_name,
                  p.logo_url AS platform_logo,
                  bp.display_order,
                  bp.is_active,
                  b.is_active AS banner_active,
                  bp.start_date,
                  bp.end_date
                FROM 
                  banner_platforms bp
                JOIN 
                  banners b ON bp.banner_id = b.banner_id
                JOIN 
                  platforms p ON bp.platform_id = p.platform_id
                ORDER BY 
                  bp.id DESC
                LIMIT 5";
      $stmt = $db->prepare($query);
      $stmt->execute();
      $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (count($assignments) > 0): ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แบนเนอร์</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แพลตฟอร์ม</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลำดับ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ระยะเวลา</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach ($assignments as $assignment):
                $is_active = $assignment['is_active'] && $assignment['banner_active'];
                $now = time();
                $start_date = strtotime($assignment['start_date']);
                $end_date = strtotime($assignment['end_date']);
                $is_within_date = ($now >= $start_date && $now <= $end_date);
              ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <?php if (!empty($assignment['banner_image'])): ?>
                        <div class="flex-shrink-0 h-10 w-16 mr-3">
                          <img class="h-10 w-16 object-cover rounded"
                            src="<?= htmlspecialchars($assignment['banner_image']) ?>"
                            alt="<?= htmlspecialchars($assignment['banner_title']) ?>"
                            onerror="this.style.display='none'">
                        </div>
                      <?php endif; ?>
                      <div class="text-sm font-medium text-blue-600">
                        <?= htmlspecialchars($assignment['banner_title']) ?>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <?php if (!empty($assignment['platform_logo'])): ?>
                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                          <img class="h-10 w-10 rounded-full object-cover"
                            src="<?= htmlspecialchars($assignment['platform_logo']) ?>"
                            alt="<?= htmlspecialchars($assignment['platform_name']) ?>"
                            onerror="this.style.display='none'">
                        </div>
                      <?php endif; ?>
                      <div class="text-sm text-gray-900">
                        <?= htmlspecialchars($assignment['platform_name']) ?>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 text-center">
                      <?= $assignment['display_order'] ?>
                    </div>
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
                      <?= date('d/m/Y H:i', strtotime($assignment['start_date'])) ?> -<br>
                      <?= date('d/m/Y H:i', strtotime($assignment['end_date'])) ?>
                    </div>
                  </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
          ยังไม่มีการกำหนดการแสดงผล
        </div>
      <?php endif; ?>
    </div>
    <div class="bg-gray-50 px-4 py-4 sm:px-6 text-right">
      <a href="banner-platforms/manage.php" class="inline-flex items-center text-blue-500 hover:text-blue-700 text-sm font-medium">
        ดูการกำหนดการแสดงผลทั้งหมด
        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>
  </div>
</div>