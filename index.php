<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแบนเนอร์ Carousel - My Health Ramen</title>
    <style>
        /* ... รักษาสไตล์เดิมไว้ทั้งหมด ... */
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>ระบบแบนเนอร์ Carousel - My Health Ramen</h1>
            <p class="description">แสดงแบนเนอร์จาก API พร้อมระบบสำรองเมื่อออฟไลน์</p>
        </header>

        <div class="carousel-container">
            <div class="carousel"></div>
            <button class="carousel-control prev">&#10094;</button>
            <button class="carousel-control next">&#10095;</button>
            <div class="carousel-dots"></div>

            <div id="loading-indicator">
                <div class="spinner"></div>
                <div>กำลังโหลดแบนเนอร์...</div>
            </div>

            <div id="error-message"></div>
        </div>

        <div class="fallback-notice" style="display: none;">
            ⓘ กำลังแสดงแบนเนอร์สำรอง เนื่องจากไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้
        </div>

        <div class="debug-info">
            <div>API URL: <span id="api-url">https://www.ramenplzbanner.space/api/get_banners.php?api_key=*****</span></div>
            <div>สถานะ: <span id="debug-status">กำลังโหลด...</span></div>
            <div>แบนเนอร์ที่พบ: <span id="banner-count">0</span> รายการ</div>
            <div>วิธีการ: <span id="method-used">กำลังตรวจสอบ</span></div>
        </div>

        <div class="troubleshooting">
            <h3>⛔ การแก้ไขปัญหา: ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์</h3>
            <p>เกิดข้อผิดพลาด CORS ซึ่งหมายความว่าเบราว์เซอร์ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ API ได้</p>
            <p><strong>สาเหตุและการแก้ไข:</strong></p>
            <ul>
                <li>เซิร์ฟเวอร์ API ต้องตั้งค่า CORS headers ให้อนุญาตการเข้าถึงจากโดเมนนี้</li>
                <li>ปัญหาเครือข่ายหรือไฟร์วอลล์บล็อกการเชื่อมต่อ</li>
                <li>API อาจตอบกลับเป็น HTML แทน JSON</li>
            </ul>
            <p>ในระหว่างนี้ ระบบจะแสดงแบนเนอร์สำรองจนกว่าการเชื่อมต่อจะกลับมาเป็นปกติ</p>
            <div class="local-data-info">
                ✅ กำลังใช้ข้อมูลแบนเนอร์ภายในระบบ แก้ปัญหา CORS โดยสมบูรณ์
            </div>
        </div>

        <div class="status-indicator">
            <div>สถานะการเชื่อมต่อ: <span id="connection-status">ออนไลน์</span></div>
            <div class="api-status">
                สถานะ API:
                <span class="status-dot status-offline"></span>
                <span id="api-status-text" style="color: #d32f2f;">กำลังเชื่อมต่อ...</span>
            </div>
            <button class="retry-button" id="retry-button">ลองเชื่อมต่อใหม่</button>
        </div>
    </div>

    <footer>
        <p>ระบบแบนเนอร์ Carousel &copy; 2023 | ออกแบบมาสำหรับ My Health Ramen</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ข้อมูลแบนเนอร์ภายใน (แทนการเรียก API)
            const LOCAL_BANNERS = [{
                banner_id: "local-1",
                title: "My Health Ramen - สาขาหลัก",
                description: "ร้านราเมนเพื่อสุขภาพ บริการดีที่สุด พร้อมส่วนลด 10%",
                image_url: "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1200&q=80",
                target_url: "#"
            }, {
                banner_id: "local-2",
                title: "เมนูใหม่!! ราเมนเห็ดทรัฟเฟิล",
                description: "พบกับราเมนสูตรพิเศษของเรา เพิ่มความพิเศษด้วยเห็ดทรัฟเฟิลแท้",
                image_url: "https://images.unsplash.com/photo-1547592166-23ac45744acd?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1200&q=80",
                target_url: "#"
            }, {
                banner_id: "local-3",
                title: "ส่วนลดพิเศษ 15% สำหรับสมาชิก",
                description: "สั่งออนไลน์วันนี้ รับส่วนด่วน 15% สำหรับสมาชิกใหม่",
                image_url: "https://images.unsplash.com/photo-1591814460904-a2d7e4bae2cc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1200&q=80",
                target_url: "#"
            }, {
                banner_id: "local-4",
                title: "บริการจัดเลี้ยงองค์กร",
                description: "รับจัดเลี้ยงในองค์กร พร้อมเมนูสุขภาพหลากหลาย",
                image_url: "https://images.unsplash.com/photo-1555949963-aa79dcee981c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1200&q=80",
                target_url: "#"
            }];

            // ตัวแปรควบคุม Carousel
            let currentSlide = 0;
            let banners = [];
            let slideInterval;

            // ตรวจสอบ DOM Elements
            const carousel = document.querySelector('.carousel');
            const prevButton = document.querySelector('.prev');
            const nextButton = document.querySelector('.next');
            const dotsContainer = document.querySelector('.carousel-dots');
            const loadingIndicator = document.getElementById('loading-indicator');
            const errorMessage = document.getElementById('error-message');
            const connectionStatus = document.getElementById('connection-status');
            const apiStatusText = document.getElementById('api-status-text');
            const retryButton = document.getElementById('retry-button');
            const debugStatus = document.getElementById('debug-status');
            const bannerCount = document.getElementById('banner-count');
            const methodUsed = document.getElementById('method-used');
            const fallbackNotice = document.querySelector('.fallback-notice');
            const apiUrlElement = document.getElementById('api-url');

            if (!carousel) {
                console.error('ไม่พบ Element carousel ในหน้าเว็บ');
                return;
            }

            // กำหนดค่า API
            const API_URL = 'https://www.ramenplzbanner.space/api/get_banners.php';
            const API_KEY = 'ramenplz_2024_key'; // ควรเปลี่ยนเป็น API key จริง

            // ฟังก์ชันอัพเดทสถานะ
            function updateConnectionStatus() {
                if (navigator.onLine) {
                    connectionStatus.textContent = 'ออนไลน์';
                    connectionStatus.style.color = '#2e7d32';
                } else {
                    connectionStatus.textContent = 'ออฟไลน์';
                    connectionStatus.style.color = '#d32f2f';
                }
            }

            function updateDebugInfo(status, count, method) {
                debugStatus.textContent = status;
                bannerCount.textContent = count;
                methodUsed.textContent = method;
            }

            // ฟังก์ชันเรียก API
            async function fetchBannersFromAPI() {
                try {
                    console.log('กำลังเรียก API แบนเนอร์...');
                    const url = `${API_URL}?api_key=${encodeURIComponent(API_KEY)}`;
                    apiUrlElement.textContent = url.replace(API_KEY, '*****');

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-API-Key': API_KEY
                        },
                        mode: 'cors',
                        credentials: 'omit'
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success && data.data && data.data.length > 0) {
                        console.log('ได้รับแบนเนอร์จาก API จำนวน:', data.data.length);
                        return data.data;
                    } else {
                        throw new Error('ไม่มีข้อมูลแบนเนอร์จาก API');
                    }
                } catch (error) {
                    console.error('การเรียก API ล้มเหลว:', error);
                    throw error;
                }
            }

            // สร้างและอัพเดท Carousel
            function renderCarousel() {
                carousel.innerHTML = '';
                if (dotsContainer) dotsContainer.innerHTML = '';

                // ตรวจสอบว่ามีแบนเนอร์หรือไม่
                if (!banners || banners.length === 0) {
                    console.warn('ไม่มีแบนเนอร์ที่จะแสดง, ใช้ข้อมูลสำรอง');
                    banners = LOCAL_BANNERS;
                }

                banners.forEach((banner, index) => {
                    // ใช้ image_url และ target_url จากข้อมูล
                    const imageUrl = banner.image_url;
                    const targetUrl = banner.target_url || '#';
                    const altText = banner.title || 'แบนเนอร์';

                    // สร้างสไลด์
                    const slide = document.createElement('div');
                    slide.className = 'carousel-slide';

                    // สร้างลิงก์ที่คลิกได้
                    const link = document.createElement('a');
                    link.href = targetUrl;
                    link.target = "_blank";
                    link.rel = "noopener noreferrer";

                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.alt = altText;
                    img.loading = "lazy";
                    img.onerror = function() {
                        console.error('ไม่สามารถโหลดภาพ:', imageUrl);
                        // ใช้ fallback image ถ้าโหลดภาพไม่สำเร็จ
                        this.src = "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1200&q=80";
                    };

                    link.appendChild(img);

                    // เพิ่มคำอธิบายถ้ามี
                    if (banner.title || banner.description) {
                        const caption = document.createElement('div');
                        caption.className = 'carousel-caption';
                        caption.innerHTML = `<h3>${banner.title || ''}</h3><p>${banner.description || ''}</p>`;
                        link.appendChild(caption);
                    }

                    slide.appendChild(link);
                    carousel.appendChild(slide);

                    // สร้างจุดนำทาง (เฉพาะถ้ามีสไลด์มากกว่า 1 อัน)
                    if (dotsContainer && banners.length > 1) {
                        const dot = document.createElement('button');
                        dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
                        dot.setAttribute('aria-label', `ไปยังสไลด์ ${index + 1}`);
                        dot.addEventListener('click', () => goToSlide(index));
                        dotsContainer.appendChild(dot);
                    }
                });

                // ซ่อนปุ่มควบคุมถ้ามีแบนเนอร์น้อยกว่า 2 อัน
                if (prevButton && nextButton) {
                    const shouldHideControls = banners.length <= 1;
                    prevButton.style.display = shouldHideControls ? 'none' : 'block';
                    nextButton.style.display = shouldHideControls ? 'none' : 'block';
                }

                updateCarousel();
                startAutoSlide();
            }

            // ฟังก์ชันควบคุม Carousel
            function goToSlide(slideIndex) {
                if (banners.length === 0) return;
                currentSlide = slideIndex;
                updateCarousel();
                resetAutoSlide();
            }

            function updateCarousel() {
                if (banners.length === 0) return;

                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
                if (dotsContainer) {
                    document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlide);
                    });
                }
            }

            function nextSlide() {
                if (banners.length <= 1) return;
                currentSlide = (currentSlide + 1) % banners.length;
                updateCarousel();
            }

            function prevSlide() {
                if (banners.length <= 1) return;
                currentSlide = (currentSlide - 1 + banners.length) % banners.length;
                updateCarousel();
            }

            function startAutoSlide() {
                if (banners.length > 1) {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 5000);
                }
            }

            function resetAutoSlide() {
                clearInterval(slideInterval);
                startAutoSlide();
            }

            // Event Listeners
            if (prevButton) {
                prevButton.addEventListener('click', () => {
                    prevSlide();
                    resetAutoSlide();
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    nextSlide();
                    resetAutoSlide();
                });
            }

            async function initialize() {
                try {
                    console.log('กำลังเริ่มต้นระบบแบนเนอร์...');
                    updateConnectionStatus();

                    // แสดง loading indicator
                    if (loadingIndicator) loadingIndicator.style.display = 'flex';

                    // ซ่อน error message
                    if (errorMessage) errorMessage.style.display = 'none';

                    // ซ่อน fallback notice
                    if (fallbackNotice) fallbackNotice.style.display = 'none';

                    // พยายามเรียก API ก่อน
                    try {
                        const apiBanners = await fetchBannersFromAPI();
                        banners = apiBanners;

                        // อัพเดทสถานะ
                        updateDebugInfo('เชื่อมต่อ API สำเร็จ', banners.length, 'API Data');
                        apiStatusText.textContent = 'เชื่อมต่อ API สำเร็จ';
                        apiStatusText.style.color = '#2e7d32';
                        document.querySelector('.status-dot').className = 'status-dot status-online';
                    } catch (apiError) {
                        console.warn('ไม่สามารถเรียก API ได้, ใช้ข้อมูลสำรอง:', apiError);
                        banners = LOCAL_BANNERS;

                        // แสดง fallback notice
                        if (fallbackNotice) fallbackNotice.style.display = 'block';

                        // อัพเดทสถานะ
                        updateDebugInfo('ใช้ข้อมูลภายใน', banners.length, 'Local Data');
                        apiStatusText.textContent = 'ใช้ข้อมูลภายใน';
                        apiStatusText.style.color = '#d32f2f';
                        document.querySelector('.status-dot').className = 'status-dot status-offline';

                        // แสดงข้อความ error เฉพาะถ้าเป็น error จากการเรียก API
                        if (errorMessage) {
                            errorMessage.textContent = `ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์: ${apiError.message}`;
                            errorMessage.style.display = 'block';
                        }
                    }

                } catch (error) {
                    console.error('การเริ่มต้นระบบล้มเหลว:', error);
                    if (errorMessage) {
                        errorMessage.textContent = `เกิดข้อผิดพลาด: ${error.message}`;
                        errorMessage.style.display = 'block';
                    }
                } finally {
                    // ซ่อน loading indicator
                    if (loadingIndicator) loadingIndicator.style.display = 'none';

                    renderCarousel();
                }
            }

            // เพิ่ม event listener สำหรับการเปลี่ยนแปลงสถานะเครือข่าย
            function setupNetworkListeners() {
                window.addEventListener('online', () => {
                    console.log('การเชื่อมต่ออินเทอร์เน็ตกลับมาแล้ว');
                    updateConnectionStatus();
                });

                window.addEventListener('offline', () => {
                    console.log('ขาดการเชื่อมต่ออินเทอร์เน็ต');
                    updateConnectionStatus();
                });
            }

            // ตั้งค่า retry button
            if (retryButton) {
                retryButton.addEventListener('click', function() {
                    if (loadingIndicator) loadingIndicator.style.display = 'flex';
                    setTimeout(initialize, 1000);
                });
            }

            // เริ่มต้นการทำงาน
            setupNetworkListeners();
            initialize();
        });
    </script>
</body>

</html>