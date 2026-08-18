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
                <a class="active">Quản Lý Sân</a>
                <a href="../pages/ql_khung_gio.php">Quản Lý Khung Giờ</a>
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

        <div class="section-label-wrap">
            <span class="section-label">
                <i class="fas fa-layer-group"></i>Khu Vực:
            </span>
        </div>
        <div id="khuVucList" class="chip-list"></div>

        <div id="sanToolbar" class="toolbar" style="display:none;">
            <div class="toolbar-left">
                <span class="toolbar-label">
                    <i class="fas fa-table-tennis-paddle-ball"></i>Sân:
                </span>
                <button class="filter-tt filter-btn active" data-filter="all">Tất cả</button>
                <button class="filter-tt filter-btn" data-filter="hoat_dong">Hoạt Động</button>
                <button class="filter-tt filter-btn" data-filter="bao_tri">Bảo Trì</button>
            </div>
            <button id="btnThemSan" class="btn-add-sm">
                <i class="fas fa-plus"></i> Thêm Sân
            </button>
        </div>

        <div id="sanList" class="card-grid"></div>
    </div>

    <div id="modalSan" class="modal-overlay">
        <div class="modal-box">
            <h3 id="modalSanTitle" class="modal-title">Thêm Sân</h3>
            <form id="formSan">
                <input type="hidden" id="sanId">
                <div class="form-group">
                    <label>Tên Sân</label>
                    <input type="text" id="sanName" required>
                </div>
                <div class="form-group">
                    <label>Trạng Thái</label>
                    <select id="sanStatus">
                        <option value="hoat_dong">Hoạt Động</option>
                        <option value="bao_tri">Bảo Trì</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="closeModal('modalSan')">Hủy</button>
                    <button type="submit" class="btn-primary" id="sanSubmitBtn">Thêm</button>
                </div>
            </form>
        </div>
    </div>

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
    let allSanData = []; // lưu toàn bộ sân để lọc phía client
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

    // ngay
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

    // modal
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    document.querySelectorAll('[id^="modal"]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });

    // tb
    function showToast(msg, ok = true) {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 10);
        setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    //load co so
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
        document.getElementById('sanToolbar').style.display = 'none';
        document.getElementById('sanList').innerHTML = '';
        loadKhuVuc(coSoId);
    }

    // khu vuc
    async function loadKhuVuc(coSoId) {
        const res = await fetch(`../api/get_khu_vuc.php?co_so_id=${coSoId}`);
        const result = await res.json();
        const container = document.getElementById('khuVucList');
        if (!result.success || result.data.length === 0) {
            container.innerHTML = '<span class="empty-text">Chưa có khu vực nào</span>';
            return;
        }
        container.innerHTML = `
            <div class="filter-chip active" data-kv-id="all">Tất cả</div>
        ` + result.data.map(kv => `
            <div class="filter-chip" data-kv-id="${kv.khu_vuc_id}">
                ${kv.ten_kv}
            </div>
        `).join('');

        // chon kv
        container.addEventListener('click', e => {
            const chip = e.target.closest('.filter-chip');
            if (!chip) return;
            container.querySelectorAll('.filter-chip').forEach(c => {
                c.classList.remove('active');
            });
            chip.classList.add('active');
            currentKhuVucId = chip.dataset.kvId;
            document.getElementById('sanToolbar').style.display = 'flex';
            // Ẩn nút Thêm Sân khi chọn Tất cả
            document.getElementById('btnThemSan').style.display = currentKhuVucId === 'all' ? 'none' : '';
            if (currentKhuVucId === 'all') {
                loadSanAll(currentCoSoId);
            } else {
                loadSan(currentKhuVucId);
            }
        });

        // all
        currentKhuVucId = 'all';
        document.getElementById('sanToolbar').style.display = 'flex';
        document.getElementById('btnThemSan').style.display = 'none';
        loadSanAll(currentCoSoId);
    }

    // load san
    async function loadSanAll(coSoId) {
        const res = await fetch(`../api/get_san_all.php?co_so_id=${coSoId}`);
        const result = await res.json();
        allSanData = result.success ? result.data : [];
        renderSan();
    }

    async function loadSan(kvId) {
        const res = await fetch(`../api/get_san_all.php?khu_vuc_id=${kvId}`);
        const result = await res.json();
        allSanData = result.success ? result.data : [];
        renderSan();
    }

    function renderSan() {
        const container = document.getElementById('sanList');
        let data = allSanData;
        if (currentFilter !== 'all') {
            data = data.filter(s => s.trang_thai === currentFilter);
        }
        if (data.length === 0) {
            container.innerHTML = '<span class="empty-text-lg">Không có sân nào</span>';
            return;
        }
        container.innerHTML = data.map(san => {
            const isActive = san.trang_thai === 'hoat_dong';
            const statusText = isActive ? 'Hoạt động' : 'Bảo trì';
            const statusClass = isActive ? 'active' : 'maintenance';
            return `
            <div class="card card-fixed">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-table-tennis-paddle-ball"></i>${san.ten_san}
                        ${san.ten_kv ? `<span class="kv-tag">(${san.ten_kv})</span>` : ''}
                    </div>
                    <span class="badge-status ${statusClass}">${statusText}</span>
                </div>
                <div class="card-actions">
                    <button onclick="openSuaSan(${san.san_id}, '${san.ten_san.replace(/'/g, "\\'")}'  , '${san.trang_thai}')" class="btn-facility-edit">
                        <i class="fas fa-pen"></i> Sửa
                    </button>
                    <button onclick="confirmXoaSan(${san.san_id}, '${san.ten_san.replace(/'/g, "\\'")}'  )" class="btn-facility-delete">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
            `;
        }).join('');
    }

    // trang thai san
    document.querySelectorAll('.filter-tt').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-tt').forEach(b => {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderSan();
        });
    });

    //them san
    document.getElementById('btnThemSan').addEventListener('click', () => {
        document.getElementById('modalSanTitle').textContent = 'Thêm Sân';
        document.getElementById('sanSubmitBtn').textContent = 'Thêm';
        document.getElementById('sanId').value = '';
        document.getElementById('sanName').value = '';
        document.getElementById('sanStatus').value = 'hoat_dong';
        openModal('modalSan');
    });

    // sua san
    function openSuaSan(id, name, status) {
        document.getElementById('modalSanTitle').textContent = 'Sửa Sân';
        document.getElementById('sanSubmitBtn').textContent = 'Cập Nhật';
        document.getElementById('sanId').value = id;
        document.getElementById('sanName').value = name;
        document.getElementById('sanStatus').value = status;
        openModal('modalSan');
    }

    // submit form 
    document.getElementById('formSan').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('sanId').value;
        const fd = new FormData();
        fd.append('ten_san', document.getElementById('sanName').value.trim());
        fd.append('trang_thai', document.getElementById('sanStatus').value);

        let url;
        if (id) {
            fd.append('san_id', id);
            url = '../api/sua_san.php';
        } else {
            fd.append('khu_vuc_id', currentKhuVucId);
            url = '../api/them_san.php';
        }
        const res = await fetch(url, { method: 'POST', body: fd });
        const result = await res.json();
        if (result.success) {
            showToast(result.message);
            closeModal('modalSan');
            if (currentKhuVucId === 'all'){
                loadSanAll(currentCoSoId);
            } else loadSan(currentKhuVucId);

        } else {
            showToast(result.error, false);
        }
    });

    // xóa sân
    function confirmXoaSan(id, name) {
        document.getElementById('xoaMessage').innerHTML = `Xóa sân <strong>${name}</strong>?`;
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('san_id', id);
            const res = await fetch('../api/xoa_san.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
            if (currentKhuVucId === 'all'){
                loadSanAll(currentCoSoId);
            } else loadSan(currentKhuVucId);
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