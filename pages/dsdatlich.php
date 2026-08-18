<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div class="logo">
                <img src="../assets/img/logo.png" class="logo-img">
                <span>PickleBall UTT</span>
            </div>
            <nav class="nav-links">
                <a class="active">Danh Sách Lịch Đặt</a>
                <a href="../pages/qlcs.php">Quản Lý Cơ Sở</a>
                <a href="../pages/qlsan.php">Quản Lý Sân</a>
                <a href="../pages/ql_khung_gio.php">Quản Lý Khung Giờ</a>
                <a href="../pages/quanlydichvu.php">Quản Lý Dịch Vụ </a>
                <a href="../pages/qlkh.php">Quản Lý Khách Hàng </a>
                <a href="../pages/bc_tk.php">Báo Cáo Thống Kê </a>
            </nav>
            <div class="user-actions">
            </div>
        </header>

        <div class="location-bar">
            <div class="location-label">
                <i class="fas fa-map-pin"></i>
                <span>Cơ Sở:</span>
            </div>
            <div class="facility-tabs" id="facilityTabs">
            </div>
            <div class="quick-date">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </div>
    </div>

    <!-- Booking Management Section -->
    <div class="container">
        <div class="management-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-calendar-check"></i> Danh Sách Lịch Đặt</h2>
                <div class="filter-bar">
                    <div class="search-filter">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" oninput="renderBookings()">
                    </div>
                    <div class="date-filter">
                        <label for="filterDate"><i class="fas fa-filter"></i> Lọc ngày:</label>
                        <input type="date" id="filterDate" class="filter-date-input">
                        <button class="btn-clear-filter" id="btnClearDate" title="Xóa lọc ngày">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="status-tabs">
                <button class="status-tab active" data-status="cho_xac_nhan">
                    <i class="fas fa-clock"></i> Chờ Xác Nhận
                    <span class="tab-count" id="countCho">0</span>
                </button>
                <button class="status-tab" data-status="da_xac_nhan">
                    <i class="fas fa-check-circle"></i> Đã Xác Nhận
                    <span class="tab-count" id="countXacNhan">0</span>
                </button>
                <button class="status-tab" data-status="hoan_thanh">
                    <i class="fas fa-flag-checkered"></i> Hoàn Thành
                    <span class="tab-count" id="countHoanThanh">0</span>
                </button>
                <button class="status-tab" data-status="da_huy">
                    <i class="fas fa-ban"></i> Đã Hủy
                    <span class="tab-count" id="countHuy">0</span>
                </button>
            </div>

            <!-- Booking Table -->
            <div class="customer-table">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>SĐT</th>
                            <th>Sân</th>
                            <th>Khu Vực</th>
                            <th>Ngày Đặt</th>
                            <th>Khung Giờ</th>
                            <th>Tổng Tiền</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        <tr>
                            <td colspan="9" style="text-align:center; padding:40px; color:#94a3b8;">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Chi Tiết Đơn -->
    <div class="modal" id="modalChiTiet">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <span>Chi Tiết Đơn Đặt Sân</span>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div id="modalBody"></div>
            <div class="modal-buttons">
                <button class="btn-outline" onclick="document.getElementById('modalChiTiet').style.display='none'">Đóng</button>
            </div>
        </div>
    </div>

    <script>
    function updateCurrentDate() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('vi-VN', options);
    }
    updateCurrentDate();

    const user = JSON.parse(localStorage.getItem('user'));
    let currentCoSoId = null;
    let currentStatus = 'cho_xac_nhan';
    let allBookings = []; 

    function showToast(msg, ok = true) {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 10);
        setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    const userActions = document.querySelector('.user-actions');
    if (user && user.vai_tro === 'khach_hang') {
        window.location.href = '../index.php';
    }
    if (user) {
        userActions.innerHTML = `
            <div class="user-profile">
                <img src="../${user.avatar || 'assets/img/default-avatar.png'}" 
                     alt="${user.ten_hien_thi}" class="user-avatar"
                     onerror="this.src='../assets/img/default-avatar.png'">
                <span class="user-name">${user.ho_ten}</span>
                <div class="user-dropdown">
                    <button class="dropdown-toggle"><i class="fas fa-chevron-down"></i></button>
                    <div class="dropdown-menu">
                        <a href="tai_khoan_chu.php"><i class="far fa-user-circle"></i> Tài Khoản</a>
                        <hr>
                        <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>`;
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('user');
            window.location.href = 'dang_nhap.php';
        });
    }

    document.addEventListener('click', (e) => {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && dropdown.contains(e.target)) {
            dropdown.querySelector('.dropdown-menu').style.display = 'block';
        } else {
            const menu = document.querySelector('.dropdown-menu');
            if (menu) menu.style.display = 'none';
        }
    });

    //chọn cơ sở
    function getQueryParam(param) {
        return new URLSearchParams(window.location.search).get(param);
    }

    async function loadFacilities() {
        try {
            const response = await fetch('../api/get_co_so.php');
            const result = await response.json();
            if (!result.success) return;
            const css = result.data;
            if (css.length > 0) {
                const urlCoSoId = getQueryParam('co_so_id');
                let targetCsId = css[0].co_so_id;
                if (urlCoSoId && css.some(cs => cs.co_so_id == urlCoSoId)) {
                    targetCsId = urlCoSoId;
                }
                renderFacilityTabs(css, targetCsId);
                onFacilityChanged(targetCsId);
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
            if (cs.co_so_id == activeId) btn.classList.add('active');
            btn.dataset.coSoId = cs.co_so_id;
            btn.textContent = cs.ten_co_so;
            tabsContainer.appendChild(btn);
        });
        tabsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('facility-btn')) {
                document.querySelectorAll('.facility-btn').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');
                const coSoId = e.target.dataset.coSoId;
                window.history.pushState({}, '', `?co_so_id=${coSoId}`);
                onFacilityChanged(coSoId);
            }
        });
    }

    function onFacilityChanged(coSoId) {
        currentCoSoId = coSoId;
        loadBookings();
    }

    // tab
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentStatus = tab.dataset.status;
            renderBookings();
        });
    });

    document.getElementById('filterDate').addEventListener('change', () => loadBookings());
    document.getElementById('btnClearDate').addEventListener('click', () => {
        document.getElementById('filterDate').value = '';
        loadBookings();
    });

    // load đơn đặt
    async function loadBookings() {
        if (!currentCoSoId) return;
        const filterDate = document.getElementById('filterDate').value;
        let url = `../api/get_dat_san_by_cs.php?co_so_id=${currentCoSoId}`;
        if (filterDate) url += `&ngay=${filterDate}`;

        try {
            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                allBookings = result.data;
                updateCounts();
                renderBookings();
            }
        } catch (error) {
            console.error('Lỗi load đơn đặt:', error);
        }
    }

    function updateCounts() {
        const counts = { cho_xac_nhan: 0, da_xac_nhan: 0, hoan_thanh: 0, da_huy: 0 };
        allBookings.forEach(b => { if (counts[b.trang_thai] !== undefined) counts[b.trang_thai]++; });
        document.getElementById('countCho').textContent = counts.cho_xac_nhan;
        document.getElementById('countXacNhan').textContent = counts.da_xac_nhan;
        document.getElementById('countHoanThanh').textContent = counts.hoan_thanh;
        document.getElementById('countHuy').textContent = counts.da_huy;
    }

    function renderBookings() {
        const tbody = document.getElementById('bookingTableBody');
        const keyword = (document.getElementById('searchInput').value || '').trim().toLowerCase();
        const filtered = allBookings.filter(b => {
            if (b.trang_thai !== currentStatus) return false;
            if (!keyword) return true;
            return String(b.dat_san_id).includes(keyword)
                || (b.ho_ten || '').toLowerCase().includes(keyword)
                || (b.sdt || '').includes(keyword)
                || (b.ten_san || '').toLowerCase().includes(keyword)
                || (b.ten_kv || '').toLowerCase().includes(keyword);
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:40px; color:#94a3b8;">
                <i class="fas fa-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                Không có đơn nào</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(b => {
            const ngay = new Date(b.ngay_dat).toLocaleDateString('vi-VN');
            const gio = `${b.gio_bat_dau.substring(0,5)} - ${b.gio_ket_thuc.substring(0,5)}`;
            const tong = Number(b.tong_hoa_don).toLocaleString('vi-VN') + 'đ';
            let actions = '';

            if (currentStatus === 'cho_xac_nhan') {
                actions = `
                    <button class="btn-reject" onclick="updateStatus(${b.dat_san_id}, 'da_huy')">
                        <i class="fas fa-times"></i> Từ chối
                    </button>
                    <button class="btn-confirm" onclick="updateStatus(${b.dat_san_id}, 'da_xac_nhan')">
                        <i class="fas fa-check"></i> Xác nhận
                    </button>
                    <button class="btn-detail" onclick="showDetail(${b.dat_san_id})">
                    <i class="fas fa-eye"></i> Xem
                    </button>`;
            } else if (currentStatus === 'da_xac_nhan') {
                actions = `
                    <button class="btn-reject" onclick="updateStatus(${b.dat_san_id}, 'da_huy')">
                        <i class="fas fa-ban"></i> Hủy
                    </button>
                    <button class="btn-complete" onclick="updateStatus(${b.dat_san_id}, 'hoan_thanh')">
                        <i class="fas fa-flag-checkered"></i> Hoàn thành
                    </button>
                    <button class="btn-detail" onclick="showDetail(${b.dat_san_id})">
                    <i class="fas fa-eye"></i> Xem
                </button>`;
            } else {
                actions = `<button class="btn-detail" onclick="showDetail(${b.dat_san_id})">
                    <i class="fas fa-eye"></i> Xem
                </button>`;
            }

            return `<tr>
                <td><strong>#${b.dat_san_id}</strong></td>
                <td>${b.ho_ten}</td>
                <td>${b.sdt || '-'}</td>
                <td>${b.ten_san}</td>
                <td>${b.ten_kv}</td>
                <td>${ngay}</td>
                <td>${gio}</td>
                <td><strong>${tong}</strong></td>
                <td class="action-buttons">${actions}</td>
            </tr>`;
        }).join('');
    }

    // cập nhật trạng thái đơn
    async function updateStatus(datSanId, newStatus) {
        const labels = {
            'da_xac_nhan': 'xác nhận',
            'da_huy': currentStatus === 'cho_xac_nhan' ? 'từ chối' : 'hủy',
            'hoan_thanh': 'hoàn thành'
        };
        if (!confirm(`Bạn có chắc muốn ${labels[newStatus]} đơn #${datSanId}?`)) return;

        try {
            const response = await fetch('../api/cap_nhat_trang_thai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ dat_san_id: datSanId, trang_thai: newStatus })
            });
            const result = await response.json();
            if (result.success) {
                showToast('Cập nhật trạng thái thành công');
                loadBookings();
            } else {
                showToast('Lỗi: ' + result.error, false);
            }
        } catch (error) {
            showToast('Lỗi kết nối server', false);
        }
    }

    function showDetail(datSanId) {
        const b = allBookings.find(x => x.dat_san_id == datSanId);
        if (!b) return;
        const ngay = new Date(b.ngay_dat).toLocaleDateString('vi-VN');
        const gio = `${b.gio_bat_dau.substring(0,5)} - ${b.gio_ket_thuc.substring(0,5)}`;
        const statusLabels = {
            'cho_xac_nhan': '<span class="badge badge-pending">Chờ xác nhận</span>',
            'da_xac_nhan': '<span class="badge badge-confirmed">Đã xác nhận</span>',
            'hoan_thanh': '<span class="badge badge-completed">Hoàn thành</span>',
            'da_huy': '<span class="badge badge-cancelled">Đã hủy</span>'
        };
        console.log("Booking detail:", b);
console.log("Dịch vụ:", b.dich_vu);
        let dvHtml = `
            <p style="margin-top:16px; color: #666;">
                Không có dịch vụ đi kèm
            </p>
        `;
        if (b.dich_vu && b.dich_vu.length > 0) {
            dvHtml = `<h4 style="margin-top:16px;">Dịch vụ đi kèm:</h4>
                <table class="detail-service-table">
                    <tr><th>Dịch vụ</th><th>SL</th><th>Thành tiền</th></tr>
                    ${b.dich_vu.map(dv => `<tr><td>${dv.ten_dich_vu}</td><td>${dv.so_luong}</td><td>${Number(dv.thanh_tien).toLocaleString('vi-VN')}đ</td></tr>`).join('')}
                </table>`;
        }
        document.getElementById('modalBody').innerHTML = `
            <div class="detail-grid">
                <div class="detail-row"><span class="detail-label">Mã đơn:</span><span>#${b.dat_san_id}</span></div>
                <div class="detail-row"><span class="detail-label">Trạng thái:</span>${statusLabels[b.trang_thai]}</div>
                <div class="detail-row"><span class="detail-label">Khách hàng:</span><span>${b.ho_ten}</span></div>
                <div class="detail-row"><span class="detail-label">SĐT:</span><span>${b.sdt || '-'}</span></div>
                <div class="detail-row"><span class="detail-label">Email:</span><span>${b.email || '-'}</span></div>
                <div class="detail-row"><span class="detail-label">Sân:</span><span>${b.ten_san} - ${b.ten_kv}</span></div>
                <div class="detail-row"><span class="detail-label">Ngày đặt:</span><span>${ngay}</span></div>
                <div class="detail-row"><span class="detail-label">Khung giờ:</span><span>${gio}</span></div>
                <div class="detail-row"><span class="detail-label">Tiền sân:</span><span>${Number(b.tien_san).toLocaleString('vi-VN')}đ</span></div>
                <div class="detail-row"><span class="detail-label">Tiền dịch vụ:</span><span>${Number(b.tien_dich_vu).toLocaleString('vi-VN')}đ</span></div>
                <div class="detail-row total"><span class="detail-label">Tổng hóa đơn:</span><span>${Number(b.tong_hoa_don).toLocaleString('vi-VN')}đ</span></div>
            </div>
            ${dvHtml}`;
        document.getElementById('modalChiTiet').style.display = 'flex';
    }

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('modalChiTiet').style.display = 'none';
    });

    loadFacilities();
    </script>
</body>
</html> 