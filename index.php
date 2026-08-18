<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
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
                <a class="active">Đặt Sân</a>
                <a href="thong_tin_co_so.php">Thông Tin Cơ Sở</a>
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
                <!-- Các tab cơ sở sẽ được vẽ bằng js với api -->
            </div>
            <div class="quick-date">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </div>

        <div class="facility-info-card" id="facilityInfo">
            <!-- Thông tin cơ sở sẽ được vẽ bằng js với api -->
        </div>

        <!-- PHẦN ĐẶT SÂN THEO KHUNG GIỜ -->
        <div class="booking-header">
            <h2 style="font-weight: 800; color: #0a3b3b;"><i class="fas fa-calendar-day"
                    style="margin-right: 12px;"></i>Chọn Sân</h2>
            <div class="booking-tools">
                <div class="date-selector" id="dateSelector">
                </div>
                <div class="date-menu" id="dateMenu" style="display: none;"></div>
            </div>
        </div>
    </div>

    <!-- Khu Vực vẽ bằng js với api -->
    <div>
        <span style="font-weight: 600; padding: 0 40px;"><i class="fas fa-map-marker-alt"></i> Khu Vực:</span>
    </div>

    <div class="court-type-filter">
    </div>

    <div>
        <span style="font-weight: 600; padding: 0 40px;"><i class="fas fa-map"></i> Sân:</span>
    </div>
    <div class="court-filter">
        <!-- Sân sẽ được vẽ bằng js với api -->
    </div>
    <div>
        <span style="font-weight: 600; padding: 0 40px;"><i class="fas fa-clock"></i> Khung Giờ:</span>
    </div>
    <!-- Grid khung giờ vẽ băng js với api -->
    <div class="time-slots-grid" id="timeSlotsContainer"></div>

    <!-- FOOTER -->
    <footer class="footer">
        <div>PickleBall UTT – Chuỗi sân PickleBall chuyên nghiệp</div>
    </footer>
    </div>
    <script>

    function getActiveDate() {
        const activeBtn = document.querySelector('.date-btn.active');
        return activeBtn ? activeBtn.dataset.date : null;
    }

    function getActiveSanId() {
        const activeChip = document.querySelector('.court-filter .filter-chip.active');
        return activeChip ? activeChip.dataset.sanId : null;
    }

    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    let currentCoSoId = null;
    let currentKhuVucId = null;

    function formatDate(date) {
        return date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function getNext7Days() {
        const days = [];
        const today = new Date();
        for (let i = 0; i < 7; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const dateString = `${year}-${month}-${day}`;
            days.push({
                label: formatDate(date),
                value: dateString
            });
        }
        return days;
    }

    const dateSelector = document.getElementById('dateSelector');
    const next7Days = getNext7Days();
    next7Days.forEach(day => {
        const btn = document.createElement('button');
        btn.className = 'date-btn';
        btn.dataset.date = day.value;
        btn.textContent = day.label;
        dateSelector.appendChild(btn);
    });

    // Xử lý khi click ngày
    dateSelector.addEventListener('click', (e) => {
        if (e.target.classList.contains('date-btn')) {
            document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            reloadTimeSlots();
        }
    });

    // Mặc định chọn ngày đầu tiên
    const firstDateBtn = dateSelector.querySelector('.date-btn');
    if (firstDateBtn) {
        firstDateBtn.classList.add('active');
    }

// lấy tham số từ URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    async function loadFacilities() {
        try {
            const response = await fetch('api/get_co_so.php');
            const result = await response.json();
            if (!result.success) {
                console.error("API trả về thất bại");
                return;
            }
            const css = result.data;
            
            if (css.length > 0) {
                // Đọc co_so_id từ URL
                const urlCoSoId = getQueryParam('co_so_id');
                let targetCsId = css[0].co_so_id; // Mặc định là cơ sở đầu tiên
                
                if (urlCoSoId && css.some(cs => cs.co_so_id == urlCoSoId)) {
                    targetCsId = urlCoSoId;
                }

                renderFacilityTabs(css, targetCsId);
                loadFacilityInfo(targetCsId);
                loadArea(targetCsId);
            }
        } catch (error) {
            console.error('Lỗi load cơ sở:', error);
        }
    }

    function renderFacilityTabs(css, activeId) {
        const tabsContainer = document.getElementById('facilityTabs');
        tabsContainer.innerHTML = '';

        css.forEach((cs) => {
            const btn = document.createElement('button');
            btn.className = 'facility-btn';
            
            // Nếu id của cơ sở hiện tại khớp với activeId thì thêm class active
            if (cs.co_so_id == activeId) {
                btn.classList.add('active');
            }
            
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
                
                window.history.pushState({}, '', `?co_so_id=${coSoId}`);
                
                loadFacilityInfo(coSoId);
                loadArea(coSoId);
            }
        });
    }

    function loadFacilityInfo(co_so_id) {
        fetch(`api/get_co_so_chi_tiet.php?co_so_id=${co_so_id}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const cs = result.data;
                    const infoContainer = document.getElementById('facilityInfo');
                    infoContainer.innerHTML = `
                        <div class="facility-gallery">
                            <div class="main-photo" style="background-image: url('${cs.anh_bia}')">
                                <span><i class="fas fa-camera"></i> ${cs.ten_co_so}</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <div class="facility-name">${cs.ten_co_so}</div>
                            <div class="facility-address"><i class="fas fa-map-marker-alt"></i> ${cs.dia_chi}</div>
                        </div>
                    `;
                } else {
                    console.error("Lỗi API:", result.error);
                }
            })
            .catch(error => console.error('Error fetching facility details:', error));
    }

    function clearCourtSelection() {
        const courtContainer = document.querySelector('.court-filter');
        const newCourtContainer = courtContainer.cloneNode(false);
        courtContainer.parentNode.replaceChild(newCourtContainer, courtContainer);
        document.getElementById('timeSlotsContainer').innerHTML =
            '<p style="grid-column:1/-1; text-align:center;">Vui lòng chọn khu vực.</p>';
    }

    function loadArea(co_so_id) {
        currentCoSoId = co_so_id;
        fetch(`api/get_khu_vuc.php?co_so_id=${co_so_id}`)
            .then(response => response.json())
            .then(result => {
                const oldContainer = document.querySelector('.court-type-filter');
                const filterContainer = oldContainer.cloneNode(false);
                oldContainer.parentNode.replaceChild(filterContainer, oldContainer);
                clearCourtSelection();

                if (result.success) {
                    const khuVucList = result.data;

                    if (khuVucList.length === 0) {
                        currentKhuVucId = null;
                        return;
                    }

                    khuVucList.forEach((kv, i) => {
                        const chip = document.createElement('div');
                        chip.className = 'filter-chip' + (i === 0 ? ' active' : '');
                        chip.dataset.khuVucId = kv.khu_vuc_id;
                        chip.textContent = kv.ten_kv;
                        filterContainer.appendChild(chip);
                    });

                    // Tự động chọn khu vực đầu tiên
                    currentKhuVucId = khuVucList[0].khu_vuc_id;
                    loadCourts(currentKhuVucId);

                    // Gán sự kiện cho các chip khu vực
                    filterContainer.addEventListener('click', (e) => {
                        if (e.target.classList.contains('filter-chip')) {
                            filterContainer.querySelectorAll('.filter-chip')
                                .forEach(chip => chip.classList.remove('active'));
                            e.target.classList.add('active');
                            currentKhuVucId = e.target.dataset.khuVucId;
                            loadCourts(currentKhuVucId);
                        }
                    });
                } else {
                    console.error("Lỗi API:", result.error);
                }
            })
            .catch(error => {
                console.error('Error fetching areas:', error);
                clearCourtSelection();
            });
    }

    function loadCourts(khu_vuc_id) {
        const url = `api/get_san_all.php?khu_vuc_id=${khu_vuc_id}`;
        fetch(url)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const sanList = result.data;
                    const oldContainer = document.querySelector('.court-filter');
                    const courtContainer = oldContainer.cloneNode(false);
                    oldContainer.parentNode.replaceChild(courtContainer, oldContainer);

                    sanList.forEach((san, i) => {
                        const chip = document.createElement('div');
                        chip.className = 'filter-chip' + (i === 0 ? ' active' : '');
                        chip.dataset.sanId = san.san_id;
                        chip.textContent = san.ten_san;
                        courtContainer.appendChild(chip);
                    });

                    // Tự động chọn sân đầu tiên và load khung giờ
                    reloadTimeSlots();

                    // Gán sự kiện cho các chip sân
                    courtContainer.addEventListener('click', (e) => {
                        if (e.target.classList.contains('filter-chip')) {
                            courtContainer.querySelectorAll('.filter-chip')
                                .forEach(chip => chip.classList.remove('active'));
                            e.target.classList.add('active');
                            reloadTimeSlots();
                        }
                    });
                } else {
                    console.error("Lỗi API:", result.error);
                }
            })
            .catch(error => console.error('Error fetching courts:', error));
    }

    function reloadTimeSlots() {
        const activeDate = getActiveDate();
        if (!activeDate) return;
        const sanId = getActiveSanId();
        if (!sanId) return;

        const url = `api/get_khung_gio_all.php?san_id=${sanId}&ngay=${activeDate}`;

        fetch(url)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    renderSlots(result.data);
                } else {
                    console.error("Lỗi API:", result.error);
                    document.getElementById('timeSlotsContainer').innerHTML =
                        '<p style="grid-column:1/-1; text-align:center;">Không thể tải khung giờ.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching time slots:', error);
                document.getElementById('timeSlotsContainer').innerHTML =
                    '<p style="grid-column:1/-1; text-align:center;">Lỗi kết nối.</p>';
            });
    }

    function renderSlots(slots) {
        const slotsGrid = document.getElementById('timeSlotsContainer');
        if (!slots || slots.length === 0) {
            slotsGrid.innerHTML = '<p style="grid-column:1/-1; text-align:center;">Không có sân phù hợp.</p>';
            return;
        }

        let html = '';
        const activeDate = getActiveDate();
        const now = new Date();
        const todayStr = now.toISOString().split('T')[0];
        const currentHHMM = now.getHours() * 60 + now.getMinutes();

        slots.forEach(slot => {
            const rawPrice = Number(slot.gia) || 0;
            const priceFormatted = rawPrice.toLocaleString('vi-VN') + 'đ';
            const startTime = slot.gio_bat_dau.substring(0, 5);
            const endTime = slot.gio_ket_thuc.substring(0, 5);
            const timeDisplay = `${startTime} - ${endTime}`;

            const status = slot.trang_thai_dat;
            let isAvailable = status === 'trong';

            // Nếu là hôm nay và khung giờ đã bắt đầu hoặc trôi qua → hết chỗ
            if (isAvailable && activeDate === todayStr) {
                const [sh, sm] = startTime.split(':').map(Number);
                if (currentHHMM >= sh * 60 + sm) {
                    isAvailable = false;
                }
            }

            const unavailableClass = isAvailable ? '' : 'da_dat';
            const buttonText = isAvailable ? 'Đặt Ngay' : 'Hết Chỗ';
            const classname = isAvailable ? 'book-now-tag' : 'book-now-tagx';
            const sanName = slot.ten_san || '';

            html += `
                <div class="slot-card ${unavailableClass}" 
                     data-id="${slot.khung_gio_id}"
                     data-san-id="${slot.san_id || ''}"
                     data-san-name="${sanName}"
                     data-start="${startTime}" 
                     data-time="${timeDisplay}" 
                     data-price="${rawPrice}"
                     data-available="${isAvailable}">
                    <div class="slot-time">${timeDisplay}</div>
                    <div class="slot-price">${priceFormatted}<small>/giờ</small></div>
                    <button class="${classname}" data-action="book" ${isAvailable ? '' : 'disabled'}>
                        ${buttonText}
                    </button>
                </div>
            `;
        });
        slotsGrid.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadFacilities();
    });

    function showBookingModal(slotData) {
        const user = JSON.parse(localStorage.getItem('user'));
        if (!user) {
            showToast('Vui lòng đăng nhập để đặt sân!', false);
            setTimeout(() => { window.location.href = 'pages/dang_nhap.php'; }, 1200);
            return;
        }

        const activeCoSo = document.querySelector('.facility-btn.active');
        const activeKV = document.querySelector('.court-type-filter .filter-chip.active');
        const activeSan = document.querySelector('.court-filter .filter-chip.active');
        const activeDate = getActiveDate();

        const sanId = slotData.sanId || (activeSan ? activeSan.dataset.sanId : '');
        const sanName = slotData.sanName || (activeSan ? activeSan.textContent : '');

        const params = new URLSearchParams({
            san_id: sanId,
            khung_gio_id: slotData.id,
            ngay_dat: activeDate,
            time: slotData.time,
            price: slotData.price,
            co_so: activeCoSo ? activeCoSo.textContent : '',
            khu_vuc: activeKV ? activeKV.textContent : '',
            san: sanName
        });

        window.location.href = 'pages/dat_san.php?' + params.toString();
    }

    document.getElementById('timeSlotsContainer').addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON' && e.target.dataset.action === 'book') {
            const slotCard = e.target.closest('.slot-card');
            const slotData = {
                id: slotCard.dataset.id,
                sanId: slotCard.dataset.sanId,
                sanName: slotCard.dataset.sanName,
                time: slotCard.dataset.time,
                price: Number(slotCard.dataset.price),
                available: slotCard.dataset.available === 'true'
            };
            if (slotData.available) {
                showBookingModal(slotData);
            }
        }
    });

    //xử lý đăng nhập hiển thị thông tin user ở header nếu đã đăng nhập
    const userActions = document.querySelector('.user-actions');
    const user = JSON.parse(localStorage.getItem('user'));
    //role là admin thì sẽ chuyển hướng sang trang quản lý, còn khách hàng thì hiển thị thông tin như bình thường
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

    function showToast(msg, ok = true) {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 10);
        setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
    }
    </script>
</body>

</html>