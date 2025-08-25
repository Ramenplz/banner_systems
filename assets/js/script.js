// assets/js/script.js
document.addEventListener('DOMContentLoaded', function () {
  // ตัวอย่างฟังก์ชัน JavaScript
  console.log('ระบบจัดการแบนเนอร์พร้อมใช้งาน');

  // แสดงตัวอย่างแบนเนอร์เมื่อ URL ภาพเปลี่ยนแปลง
  const imageUrlInput = document.getElementById('image_url');
  if (imageUrlInput) {
    imageUrlInput.addEventListener('change', function () {
      updateBannerPreview();
    });
  }

  // ฟังก์ชันแสดงตัวอย่างแบนเนอร์
  function updateBannerPreview() {
    const imageUrl = document.getElementById('image_url').value;
    const previewDiv = document.getElementById('banner-preview');

    if (previewDiv && imageUrl) {
      previewDiv.innerHTML = `
                <h3 class="font-bold mb-2">ตัวอย่างแบนเนอร์</h3>
                <img src="${imageUrl}" alt="ตัวอย่างแบนเนอร์" class="mb-2">
                <p class="text-sm text-gray-600">URL ปลายทาง: ${document.getElementById('target_url').value || 'ไม่ได้กำหนด'}</p>
            `;
    }
  }

  // ฟังก์ชันยืนยันการลบ
  const deleteButtons = document.querySelectorAll('.delete-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      if (!confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?')) {
        e.preventDefault();
      }
    });
  });
});