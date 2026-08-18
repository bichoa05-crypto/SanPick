<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/ttcs.css">
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div class="logo">
                <img src="assets/img/logo.png" style="width: 80px; height: 80px; margin-right: 8px;">
                <span>PickleBall UTT</span>
            </div>
            <nav class="nav-links">
                <a href="index.php">Đặt Sân</a>
                <a class="active">Thông Tin Cơ Sở</a>
            </nav>
            <!-- Nút đăng nhập/đăng ký nếu chưa đăng nhập, hoặc avatar và menu người dùng nếu đã đăng nhập xử lý bằng js -->
            <div class="user-actions">
                <a href="pages/dang_nhap.php"><button class="btn-outline"><i class="far fa-user"></i> Đăng
                        nhập</button></a>
                <a href="pages/dang_ky.php"><button class="btn-primary">Đăng ký</button></a>
            </div>
        </header>

        <div class="location-bar">
            <div class="location-label">
                <i class="fas fa-map-pin"></i>
                <span>Cơ Sở:</span>
            </div>
            <div class="facility-tabs" id="facilityTabs">
                <!-- vẽ cơ sở bằng js hiện tên cs -->
            </div>
        </div>
        <div class="facility-info-card" id="facilityInfo">
            <!-- Thông tin cơ sở(ten, dia chi, anh bia ) sẽ được hiển thị ở đây, vẽ bằng js với api -->
        </div>
        <label style="font-weight: 600; padding: 20px 0; display: block; text-align: center; width: 100%;">
            Một Số Hình Ảnh Của Cơ Sở
        </label>
        <!--Anh cua co so -->
        <div class="facility-gallery" id="facilityGallery">
            <!-- Hình ảnh cơ sở sẽ được hiển thị ở đây, vẽ bằng js với api -->
        </div>
    </div>
    <footer class="footer">
        <div>PickleBall UTT – Chuỗi sân PickleBall chuyên nghiệp</div>
    </footer>
        <script>
        const userActions = document.querySelector('.user-actions');
        const user = JSON.parse(localStorage.getItem('user'));
        if (user && user.vai_tro === 'chu_san') {
            window.location.href = 'pages/dsdatlich.php';
        }        
        if (user) {
            userActions.innerHTML = `
                <div class="user-profile">
                    <img src="${user.avatar || 'assets/img/default-avatar.png'}" 
                         alt="${user.ten_hien_thi}" 
                         class="user-avatar"
                         onerror="this.src='assets/img/default-avatar.png'">
                    <span class="user-name">${user.ho_ten}</span>
                    <div class="user-dropdown">
                        <button class="dropdown-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="pages/tai_khoan_khach.php"><i class="far fa-user-circle"></i> Tài Khoản</a>
                            <a href="pages/tai_khoan_khach.php?tab=history"><i class="far fa-calendar-alt"></i> Lịch Sử Đặt Sân</a>
                            <hr>
                            <a href="pages/dang_xuat.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    </div>
                </div>
        `;

            document.getElementById('logoutBtn').addEventListener('click', () => {
                localStorage.removeItem('user');
                location.reload();
            });
        } else {
            userActions.innerHTML = `
            <a href="pages/dang_nhap.php"><button class="btn-outline"><i class="far fa-user"></i> Đăng nhập</button></a>
            <a href="pages/dang_ky.php"><button class="btn-primary">Đăng ký</button></a>
        `;
        }

        document.addEventListener('click', (e) => {
            const dropdown = document.querySelector('.user-dropdown');
            if (dropdown && dropdown.contains(e.target)) {
                dropdown.querySelector('.dropdown-menu').style.display = 'block';
            } else {
                const menu = document.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.display = 'none';
                }
            }
        });

        async function loadFacilities() {
            try {
                const response = await fetch('api/get_co_so.php');
                const result = await response.json();
                if (!result.success) {
                    console.error("API trả về thất bại");
                    return;
                }
                const css = result.data;
                renderFacilityTabs(css);

                // Load cơ sở đầu tiên
                if (css.length > 0) {
                    const firstCsId = css[0].co_so_id;
                    loadFacilityInfo(firstCsId);
                    loadIImages(firstCsId);
                }
            } catch (error) {
                console.error('Lỗi load cơ sở:', error);
            }
        }

        function renderFacilityTabs(css) {
            const tabsContainer = document.getElementById('facilityTabs');
            tabsContainer.innerHTML = '';

            css.forEach((cs, index) => {
                const btn = document.createElement('button');
                btn.className = 'facility-btn';
                if (index === 0) btn.classList.add('active');
                btn.dataset.coSoId = cs.co_so_id;
                btn.textContent = cs.ten_co_so;
                tabsContainer.appendChild(btn);
            });

            tabsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('facility-btn')) {
                    document.querySelectorAll('.facility-btn')
                        .forEach(btn => btn.classList.remove('active'));
                    e.target.classList.add('active');

                    const coSoId = e.target.dataset.coSoId;
                    loadFacilityInfo(coSoId);
                    loadIImages(coSoId);
                }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            loadFacilities();
        });

        // Hàm tải thông tin chi tiết cơ sở
        async function loadFacilityInfo(coSoId) {
            try {
                // Gọi API lấy chi tiết cơ sở theo co_so_id
                const response = await fetch(`api/get_co_so_chi_tiet.php?co_so_id=${coSoId}`);
                const result = await response.json();
                
                if (!result.success) {
                    console.error("Không thể lấy thông tin cơ sở");
                    return;
                }

                const info = result.data; 
                const facilityInfoContainer = document.getElementById('facilityInfo');
                
                let htmlContent = `
                        <div class="facility-gallery">
                            <div class="main-photo" style="background-image: url('${info.anh_bia}')">
                                <span><i class="fas fa-camera"></i> ${info.ten_co_so}</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <div class="facility-name">${info.ten_co_so}</div>
                            <div class="facility-address"><i class="fas fa-map-marker-alt"></i> ${info.dia_chi}</div>
                            <div class="facility-courts"><i class="fas fa-layer-group"></i> Số lượng sân: ${info.so_san} sân</div>
                            <br>
                            <div class="btn-book">
                                <a href="index.php?co_so_id=${info.co_so_id}"><button class="btn-primary"><i class="fas fa-book"></i> Đặt Sân Ngay</button></a>
                            </div>
                        </div>
                `;
                facilityInfoContainer.innerHTML = htmlContent;
            

            } catch (error) {
                console.error('Lỗi load chi tiết cơ sở:', error);
            }
        }

        let currentSlide = 0;
        //chỉnh lại kích thước ảnh trong slider
        async function loadIImages(coSoId) {
            try {
                const response = await fetch(`api/get_anh_by_co_so.php?co_so_id=${coSoId}`);
                const result = await response.json();
                if (!result.success) {
                    console.error("Không thể lấy hình ảnh cơ sở");
                    return;
                }
                const images = result.data;
                const galleryContainer = document.getElementById('facilityGallery');
                let htmlContent = `
                    <div class="slider">
                        <div class="slider-track" id="sliderTrack">
                            ${images.map(img => `
                                <div class="slide">
                                    <img src="${img.duong_dan}" alt="Hình ảnh cơ sở" onerror="this.src='assets/img/default-image.png'">
                                </div>
                            `).join('')}
                        </div>
                        <button class="slider-btn prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                        <button class="slider-btn next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                `;
                galleryContainer.innerHTML = htmlContent;
            } catch (error) {
                console.error('Lỗi load hình ảnh cơ sở:', error);
            }
        }

        function moveSlide(direction) {
            const track = document.getElementById('sliderTrack');
            if (!track) return;
            
            const images = track.querySelectorAll('img');
            const totalSlides = images.length;

            currentSlide += direction;

            // Vòng lặp slider
            if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            } else if (currentSlide >= totalSlides) {
                currentSlide = 0;
            }   
            updateSlider();
        }

        function updateSlider() {
            const track = document.getElementById('sliderTrack');
            if (track) {
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            }
        }
        </script>
</body>