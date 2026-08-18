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
                <a href="../pages/dsdatlich.php">Danh Sách Lịch Đặt</a>
                <a href="../pages/qlcs.php">Quản Lý Cơ Sở</a>
                <a href="../pages/qlsan.php">Quản Lý Sân</a>
                <a class="active">Quản Lý Khung Giờ</a>
                <a href="../pages/quanlydichvu.php">Quản Lý Dịch Vụ</a>
                <a href="../pages/qlkh.php">Quản Lý Khách Hàng</a>
                <a href="../pages/bc_tk.php">Báo Cáo Thống Kê</a>
            </nav>
            <div class="user-actions"></div>
        </header>

        <div class="location-bar">
            <div class="location-label">
                <i class="fas fa-map-pin"></i>
                <span>Cơ Sở:</span>
            </div>
            <div class="facility-tabs" id="facilityTabs"></div>
            <div class="quick-date">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </div>

        <!-- Khu Vực -->
        <div class="section-label-wrap">
            <span class="section-label">
                <i class="fas fa-layer-group"></i>Khu Vực:
            </span>
        </div>
        <div id="khuVucList" class="chip-list"></div>

        <!-- Sân -->
        <div id="sanSection" class="section-label-wrap" style="display:none;">
            <span class="section-label">
                <i class="fas fa-table-tennis-paddle-ball"></i>Sân:
            </span>
        </div>
        <div id="sanList" class="chip-list"></div>

        <div id="kgToolbar" class="toolbar" style="display:none;">
            <div class="toolbar-left">
                <span class="toolbar-label">
                    <i class="fas fa-clock"></i>Khung Giờ:
                </span>
                <input type="date" id="ngayLoc" class="filter-date">
                <button class="filter-tt filter-btn active" data-filter="all">Tất cả</button>
                <button class="filter-tt filter-btn" data-filter="trong">Trống</button>
                <button class="filter-tt filter-btn" data-filter="da_dat">Đã đặt</button>
            </div>
            <button id="btnThemKG" class="btn-add-sm">
                <i class="fas fa-plus"></i> Thêm Khung Giờ
            </button>
        </div>

        <!-- Danh sách khung giờ -->
        <div id="khungGioList" class="card-grid"></div>
    </div>

    <!-- Modal thêm/sửa khung giờ -->
    <div id="modalKG" class="modal-overlay">
        <div class="modal-box">
            <h3 id="modalKGTitle" class="modal-title">Thêm Khung Giờ</h3>
            <form id="formKG">
                <input type="hidden" id="kgId">
                <div class="form-group">
                    <label>Giờ Bắt Đầu</label>
                    <div class="form-row" style="align-items:center;">
                        <select id="kgBatDauH" required class="time-select"></select>
                        <span class="time-separator">:</span>
                        <select id="kgBatDauM" required class="time-select"></select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Giờ Kết Thúc</label>
                    <div class="form-row" style="align-items:center;">
                        <select id="kgKetThucH" required class="time-select"></select>
                        <span class="time-separator">:</span>
                        <select id="kgKetThucM" required class="time-select"></select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Giá (VNĐ)</label>
                    <input type="number" id="kgGia" min="0" step="1000" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="closeModal('modalKG')">Hủy</button>
                    <button type="submit" class="btn-primary" id="kgSubmitBtn">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div id="modalXoa" class="modal-overlay">
        <div class="modal-box-confirm">
            <p id="xoaMessage" class="modal-message"></p>
            <div class="modal-actions-center">
                <button class="btn-outline" onclick="closeModal('modalXoa')">Hủy</button>
                <button class="btn-primary bg-danger" id="btnXacNhanXoa">Xóa</button>
            </div>
        </div>
    </div>
    <script>
    const user = JSON.parse(localStorage.getItem('user'));
    let currentCoSoId = null;
    let currentKhuVucId = null;
    let currentSanId = null;
    let allSanData = [];
    let allKGData = [];
    let currentFilter = 'all';

    const userActions = document.querySelector('.user-actions');
    if (user && user.vai_tro === 'khach_hang') {
        window.location.href = '../index.php';
    }
    if (user) {
        userActions.innerHTML = `
            <div class="user-profile">
                <img src="../${user.avatar || 'assets/img/default-avatar.png'}" alt="${user.ho_ten}" class="user-avatar" onerror="this.src='../assets/img/default-avatar.png'">
                <span class="user-name">${user.ho_ten}</span>
                <div class="user-dropdown">
                    <button class="dropdown-toggle"><i class="fas fa-chevron-down"></i></button>
                    <div class="dropdown-menu">
                        <a href="tai_khoan_chu.php"><i class="far fa-user-circle"></i> Tài Khoản</a>
                        <hr>
                        <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        `;
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

    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
    document.getElementById('ngayLoc').value = new Date().toISOString().split('T')[0];

    // khởi tạo select giờ phút
    function initTimeSelects() {
        const hours = Array.from({length:24}, (_, i) => String(i).padStart(2, '0'));
        const mins = ['00', '30'];
        ['kgBatDauH', 'kgKetThucH'].forEach(id => {
            const sel = document.getElementById(id);
            sel.innerHTML = '<option value="" disabled selected>Giờ</option>' + hours.map(h => `<option value="${h}">${h}</option>`).join('');
        });
        ['kgBatDauM', 'kgKetThucM'].forEach(id => {
            const sel = document.getElementById(id);
            sel.innerHTML = '<option value="" disabled selected>Phút</option>' + mins.map(m => `<option value="${m}">${m}</option>`).join('');
        });
    }
    initTimeSelects();

    function getTimeValue(hId, mId) {
        const h = document.getElementById(hId).value;
        const m = document.getElementById(mId).value;
        if (!h || !m) return '';
        return h + ':' + m;
    }
    function setTimeValue(hId, mId, timeStr) {
        if (!timeStr) {
            document.getElementById(hId).value = '';
            document.getElementById(mId).value = '';
            return;
        }
        const parts = timeStr.substring(0, 5).split(':');
        document.getElementById(hId).value = parts[0];
        document.getElementById(mId).value = parts[1];
    }

    // modal
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    document.querySelectorAll('[id^="modal"]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });

    function showToast(msg, ok = true) {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 10);
        setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    // lấy param
    function getQueryParam(p) { return new URLSearchParams(window.location.search).get(p); }

    async function loadFacilities() {
        const res = await fetch('../api/get_co_so.php');
        const result = await res.json();
        if (!result.success) return;
        const list = result.data;
        if (list.length === 0) {
            document.getElementById('facilityTabs').innerHTML = '<span class="empty-text">Chưa có cơ sở</span>';
            return;
        }
        const urlId = getQueryParam('co_so_id');
        let activeId = list[0].co_so_id;
        if (urlId && list.some(c => c.co_so_id == urlId)) activeId = urlId;
        renderFacilityTabs(list, activeId);
        onFacilityChanged(activeId);
    }

    function renderFacilityTabs(list, activeId) {
        const tabs = document.getElementById('facilityTabs');
        tabs.innerHTML = '';
        list.forEach(cs => {
            const btn = document.createElement('button');
            btn.className = 'facility-btn';
            if (cs.co_so_id == activeId) btn.classList.add('active');
            btn.dataset.coSoId = cs.co_so_id;
            btn.textContent = cs.ten_co_so;
            tabs.appendChild(btn);
        });
        tabs.addEventListener('click', e => {
            if (e.target.classList.contains('facility-btn')) {
                tabs.querySelectorAll('.facility-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                const id = e.target.dataset.coSoId;
                window.history.pushState({}, '', `?co_so_id=${id}`);
                onFacilityChanged(id);
            }
        });
    }

    function onFacilityChanged(coSoId) {
        currentCoSoId = coSoId;
        currentKhuVucId = null;
        currentSanId = null;
        document.getElementById('sanSection').style.display = 'none';
        document.getElementById('sanList').innerHTML = '';
        document.getElementById('kgToolbar').style.display = 'none';
        document.getElementById('khungGioList').innerHTML = '';
        loadKhuVuc(coSoId);
    }

    async function loadKhuVuc(coSoId) {
        const res = await fetch(`../api/get_khu_vuc.php?co_so_id=${coSoId}`);
        const result = await res.json();
        const container = document.getElementById('khuVucList');
        if (!result.success || result.data.length === 0) {
            container.innerHTML = '<span class="empty-text">Chưa có khu vực nào</span>';
            return;
        }
        container.innerHTML = result.data.map((kv, i) => `
            <div class="filter-chip${i === 0 ? ' active' : ''}" data-kv-id="${kv.khu_vuc_id}">
                ${kv.ten_kv}
            </div>
        `).join('');

        container.addEventListener('click', e => {
            const chip = e.target.closest('.filter-chip');
            if (!chip) return;
            container.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentKhuVucId = chip.dataset.kvId;
            currentSanId = null;
            loadSanList();
        });

        // Tự động chọn khu vực đầu tiên
        currentKhuVucId = result.data[0].khu_vuc_id;
        loadSanList();
    }

    async function loadSanList() {
        let url = `../api/get_san_all.php?khu_vuc_id=${currentKhuVucId}`;
        const res = await fetch(url);
        const result = await res.json();
        const container = document.getElementById('sanList');
        document.getElementById('sanSection').style.display = 'block';

        // Chỉ lấy sân hoạt động
        allSanData = (result.success ? result.data : []).filter(s => s.trang_thai === 'hoat_dong');
        if (allSanData.length === 0) {
            container.innerHTML = '<span class="empty-text">Không có sân hoạt động</span>';
            document.getElementById('kgToolbar').style.display = 'none';
            document.getElementById('khungGioList').innerHTML = '';
            return;
        }
        container.innerHTML = allSanData.map((s, i) => `
            <div class="filter-chip${i === 0 ? ' active' : ''}" data-san-id="${s.san_id}">
                ${s.ten_san}
            </div>
        `).join('');

        container.addEventListener('click', e => {
            const chip = e.target.closest('.filter-chip');
            if (!chip) return;
            container.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentSanId = chip.dataset.sanId;
            document.getElementById('kgToolbar').style.display = 'flex';
            loadKhungGio();
        });

        // Tự động chọn sân đầu tiên
        currentSanId = allSanData[0].san_id;
        document.getElementById('kgToolbar').style.display = 'flex';
        loadKhungGio();
    }
    
    // load khung giờ   
    async function loadKhungGio() {
        const ngay = document.getElementById('ngayLoc').value;
        let url = `../api/get_khung_gio_all.php?san_id=${currentSanId}&ngay=${ngay}`;
        const res = await fetch(url);
        const result = await res.json();
        allKGData = result.success ? result.data : [];
        renderKhungGio();
    }

    function formatTime(t) {
        return t.substring(0, 5); // HH:MM
    }
    function formatPrice(p) {
        return Number(p).toLocaleString('vi-VN') + 'đ';
    }

    function isExpired(gioBatDau) {
        const ngay = document.getElementById('ngayLoc').value;
        const now = new Date();
        const todayStr = now.toISOString().split('T')[0];
        if (ngay < todayStr) return true;
        if (ngay > todayStr) return false;
        const [h, m] = gioBatDau.substring(0, 5).split(':').map(Number);
        return now.getHours() * 60 + now.getMinutes() >= h * 60 + m;
    }

    function renderKhungGio() {
        const container = document.getElementById('khungGioList');
        let data = allKGData;

        if (currentFilter !== 'all') {
            data = data.filter(kg => {
                const expired = isExpired(kg.gio_bat_dau);
                if (currentFilter === 'da_qua') return expired;
                if (currentFilter === 'trong') return !expired && kg.trang_thai_dat === 'trong';
                if (currentFilter === 'da_dat') return kg.trang_thai_dat === 'da_dat';
                return true;
            });
        }
        if (data.length === 0) {
            container.innerHTML = '<span class="empty-text-lg">Không có khung giờ nào</span>';
            return;
        }
        container.innerHTML = data.map(kg => {
            const expired = isExpired(kg.gio_bat_dau);
            let statusText, statusClass;
            if (expired) {
                statusText = 'Đã Qua';
                statusClass = 'expired';
            } else if (kg.trang_thai_dat === 'trong') {
                statusText = 'Trống';
                statusClass = 'available';
            } else {
                statusText = 'Đã Đặt';
                statusClass = 'booked';
            }
            return `
            <div class="card card-fixed" >
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-clock"></i>${formatTime(kg.gio_bat_dau)} - ${formatTime(kg.gio_ket_thuc)}
                    </div>
                    <span class="badge-status ${statusClass}">
                        ${statusText}
                    </span>
                </div>
                ${kg.ten_san ? `<div class="card-subtitle"><i class="fas fa-table-tennis-paddle-ball"></i>${kg.ten_san}</div>` : ''}
                <div class="card-price">
                    <i class="fas fa-tag"></i>${formatPrice(kg.gia)}
                </div>
                <div class="card-actions">
                    <button class="btn-facility-edit" ${statusClass === 'booked' ? 'disabled style = "opacity: 0.5"' : ''} onclick="openSuaKG(${kg.khung_gio_id}, '${kg.gio_bat_dau}', '${kg.gio_ket_thuc}', ${kg.gia})">
                        <i class="fas fa-pen"></i> Sửa
                    </button>
                    <button class="btn-facility-delete" ${statusClass === 'booked' ? 'disabled style = "opacity: 0.5"' : ''} onclick="confirmXoaKG(${kg.khung_gio_id}, '${formatTime(kg.gio_bat_dau)} - ${formatTime(kg.gio_ket_thuc)}')">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
            `;
        }).join('');
    }

    // lọc theo trạng thái
    document.querySelectorAll('.filter-tt').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-tt').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderKhungGio();
        });
    });

    // đổi ngày thì load lại khung giờ
    document.getElementById('ngayLoc').addEventListener('change', () => {
        if (currentSanId) loadKhungGio();
    });

    // thêm khung giờ
    document.getElementById('btnThemKG').addEventListener('click', () => {
        document.getElementById('modalKGTitle').textContent = 'Thêm Khung Giờ';
        document.getElementById('kgSubmitBtn').textContent = 'Thêm';
        document.getElementById('kgId').value = '';
        setTimeValue('kgBatDauH', 'kgBatDauM', '');
        setTimeValue('kgKetThucH', 'kgKetThucM', '');
        document.getElementById('kgGia').value = '';
        openModal('modalKG');
    });

    // sửa khung giờ
    function openSuaKG(id, batDau, ketThuc, gia) {
        document.getElementById('modalKGTitle').textContent = 'Sửa Khung Giờ';
        document.getElementById('kgSubmitBtn').textContent = 'Cập Nhật';
        document.getElementById('kgId').value = id;
        setTimeValue('kgBatDauH', 'kgBatDauM', batDau);
        setTimeValue('kgKetThucH', 'kgKetThucM', ketThuc);
        document.getElementById('kgGia').value = gia;
        openModal('modalKG');
    }

    // submit form 
    document.getElementById('formKG').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('kgId').value;
        const fd = new FormData();
        const batDau = getTimeValue('kgBatDauH', 'kgBatDauM');
        const ketThuc = getTimeValue('kgKetThucH', 'kgKetThucM');
        if (!batDau || !ketThuc) { showToast('Vui lòng chọn đầy đủ giờ', false); return; }
        fd.append('gio_bat_dau', batDau);
        fd.append('gio_ket_thuc', ketThuc);
        fd.append('gia', document.getElementById('kgGia').value);

        let url;
        if (id) {
            fd.append('khung_gio_id', id);
            url = '../api/sua_khung_gio.php';
        } else {
            fd.append('san_id', currentSanId);
            url = '../api/them_khung_gio.php';
        }
        const res = await fetch(url, { method: 'POST', body: fd });
        const result = await res.json();
        if (result.success) {
            showToast(result.message);
            closeModal('modalKG');
            loadKhungGio();
        } else {
            showToast(result.error, false);
        }
    });

    // xóa khung giờ
    function confirmXoaKG(id, label) {
        document.getElementById('xoaMessage').innerHTML = `Xóa khung giờ <strong>${label}</strong>?`;
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('khung_gio_id', id);
            const res = await fetch('../api/xoa_khung_gio.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
                loadKhungGio();
            } else {
                showToast(result.error, false);
            }
        };
        openModal('modalXoa');
    }
    loadFacilities();
    </script>
</body>
</html>