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
                <a class="active">Quản Lý Cơ Sở</a>
                <a href="../pages/qlsan.php">Quản Lý Sân</a>
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
            <button id="btnThemCS" class="btn-add-cs">
                <i class="fas fa-plus"></i> Thêm
            </button>
        </div>

        <!-- Thông tin cơ sở đang chọn -->
        <div class="facility-info-card" id="facilityInfo" style="display:none;">
        </div>

        <!-- Ảnh Cơ Sở -->
        <div id="anhSection" style="display:none; margin-bottom:24px;">
            <div class="section-label-wrap" style="justify-content:space-between;">
                <span class="section-label">
                    <i class="fas fa-images"></i>Hình Ảnh:
                </span>
                <label id="btnThemAnh" class="btn-add-img">
                    <i class="fas fa-plus"></i> Thêm Ảnh
                    <input type="file" id="inputThemAnh" accept="image/*" multiple style="display:none;">
                </label>
            </div>
            <div id="anhList" class="chip-list"></div>
        </div>

        <!-- Khu vực -->
        <div class="section-label-wrap" style="justify-content:space-between;">
            <span class="section-label">
                <i class="fas fa-layer-group"></i>Khu Vực:
            </span>
            <button id="btnThemKV" class="btn-add-sm" style="display:none;">
                <i class="fas fa-plus"></i> Thêm Khu Vực
            </button>
        </div>
        <div class="court-type-filter chip-list" id="khuVucList"></div>
    </div>

    <!-- Modal thêm/sửa cơ sở -->
    <div id="modalCS" class="modal-overlay">
        <div class="modal-box">
            <h3 id="modalCSTitle" class="modal-title">Thêm Cơ Sở</h3>
            <form id="formCS" enctype="multipart/form-data">
                <input type="hidden" id="csId">
                <div class="form-group">
                    <label>Tên Cơ Sở</label>
                    <input type="text" id="csName" placeholder="Nhập tên cơ sở" required>
                </div>
                <div class="form-group">
                    <label>Địa Chỉ</label>
                    <input type="text" id="csAddr" placeholder="Nhập địa chỉ" required>
                </div>
                <div class="form-group">
                    <label>Ảnh Bìa</label>
                    <input type="file" id="csImg" accept="image/*" class="form-file-input">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="closeModal('modalCS')">Hủy</button>
                    <button type="submit" class="btn-primary" id="csSubmitBtn">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal thêm/sửa khu vực -->
    <div id="modalKV" class="modal-overlay">
        <div class="modal-box" style="max-width:400px;">
            <h3 id="modalKVTitle" class="modal-title">Thêm Khu Vực</h3>
            <form id="formKV">
                <input type="hidden" id="kvId">
                <div class="form-group">
                    <label>Tên Khu Vực</label>
                    <input type="text" id="kvName" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="closeModal('modalKV')">Hủy</button>
                    <button type="submit" class="btn-primary" id="kvSubmitBtn">Thêm</button>
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

    function getQueryParam(p) { return new URLSearchParams(window.location.search).get(p); }

    async function loadFacilities() {
        const res = await fetch('../api/get_co_so.php');
        const result = await res.json();
        if (!result.success) return;
        const list = result.data;
        if (list.length === 0) {
            document.getElementById('facilityTabs').innerHTML = '<span class="empty-text">Chưa có cơ sở nào</span>';
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
        loadFacilityInfo(coSoId);
        loadAnhCoSo(coSoId);
        loadKhuVuc(coSoId);
        document.getElementById('btnThemKV').style.display = 'inline-flex';
    }

    async function loadFacilityInfo(coSoId) {
        const res = await fetch(`../api/get_co_so_chi_tiet.php?co_so_id=${coSoId}`);
        const result = await res.json();
        if (!result.success) return;
        const cs = result.data;
        const box = document.getElementById('facilityInfo');
        box.style.display = 'flex';
        box.innerHTML = `
                        <div class="facility-gallery">
                            <div class="main-photo" style="background-image: url('../${cs.anh_bia}')">
                                <span><i class="fas fa-camera"></i> ${cs.ten_co_so}</span>
                            </div>
                        </div>
                        <div class="facility-details">
                            <div class="facility-name">${cs.ten_co_so}</div>
                            <div class="facility-address"><i class="fas fa-map-marker-alt"></i> ${cs.dia_chi}</div>
                            <div class="facility-actions">
                                <button class="btn-facility-edit" onclick="openSuaCS(${cs.co_so_id})">
                                    <i class="fas fa-pen"></i> Chỉnh sửa
                                </button>
                                <button class="btn-facility-delete" onclick="confirmXoaCS(${cs.co_so_id}, '${cs.ten_co_so.replace(/'/g, "\\\'")}')">
                                    <i class="fas fa-trash"></i> Xóa cơ sở
                                </button>
                            </div>
                        </div>
        `;
    }

    async function loadAnhCoSo(coSoId) {
        const section = document.getElementById('anhSection');
        section.style.display = 'block';
        const container = document.getElementById('anhList');
        container.innerHTML = '<span class="empty-text">Đang tải...</span>';

        const res = await fetch(`../api/get_anh_by_co_so.php?co_so_id=${coSoId}`);
        const result = await res.json();
        if (!result.success || result.data.length === 0) {
            container.innerHTML = '<span class="empty-text">Chưa có hình ảnh nào</span>';
            return;
        }
        container.innerHTML = result.data.map(anh => `
            <div class="img-thumb">
                <img src="../${anh.duong_dan}" onerror="this.src='../assets/img/default-avatar.png'">
                <button class="btn-remove-img" onclick="confirmXoaAnh(${anh.hinh_id})" title="Xóa ảnh">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    document.getElementById('inputThemAnh').addEventListener('change', async (e) => {
        const files = e.target.files;
        if (!files.length || !currentCoSoId) return;
        let count = 0;
        for (const file of files) {
            const fd = new FormData();
            fd.append('co_so_id', currentCoSoId);
            fd.append('hinh_anh', file);
            const res = await fetch('../api/them_anh_co_so.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) count++;
            else showToast(result.error, false);
        }
        if (count > 0) showToast(`Đã thêm ${count} ảnh`);
        e.target.value = '';
        loadAnhCoSo(currentCoSoId);
    });

    function confirmXoaAnh(hinhId) {
        document.getElementById('xoaMessage').innerHTML = 'Xóa ảnh này?';
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('hinh_id', hinhId);
            const res = await fetch('../api/xoa_anh_co_so.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
                loadAnhCoSo(currentCoSoId);
            } else {
                showToast(result.error, false);
            }
        };
        openModal('modalXoa');
    }

    async function loadKhuVuc(coSoId) {
        const res = await fetch(`../api/get_khu_vuc.php?co_so_id=${coSoId}`);
        const result = await res.json();
        const container = document.getElementById('khuVucList');
        if (!result.success || result.data.length === 0) {
            container.innerHTML = '<span class="empty-text">Chưa có khu vực nào</span>';
            return;
        }
        container.innerHTML = result.data.map(kv => `
            <div class="kv-chip">
                <span>${kv.ten_kv}</span>
                <div class="kv-chip-actions">
                    <button class="btn-facility-edit" onclick="openSuaKV(${kv.khu_vuc_id}, '${kv.ten_kv.replace(/'/g, "\\'")}'  )" title="Sửa">
                        <i class="fas fa-pen"></i> Sửa
                    </button>
                    <button class="btn-facility-delete" onclick="confirmXoaKV(${kv.khu_vuc_id}, '${kv.ten_kv.replace(/'/g, "\\'")}'  )" title="Xóa">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('btnThemCS').addEventListener('click', () => {
        document.getElementById('modalCSTitle').textContent = 'Thêm Cơ Sở';
        document.getElementById('csSubmitBtn').textContent = 'Thêm';
        document.getElementById('csId').value = '';
        document.getElementById('csName').value = '';
        document.getElementById('csAddr').value = '';
        document.getElementById('csImg').value = '';
        openModal('modalCS');
    });

    async function openSuaCS(id) {
        const res = await fetch(`../api/get_co_so_chi_tiet.php?co_so_id=${id}`);
        const result = await res.json();
        if (!result.success) return;
        const cs = result.data;
        document.getElementById('modalCSTitle').textContent = 'Sửa Cơ Sở';
        document.getElementById('csSubmitBtn').textContent = 'Cập Nhật';
        document.getElementById('csId').value = cs.co_so_id;
        document.getElementById('csName').value = cs.ten_co_so;
        document.getElementById('csAddr').value = cs.dia_chi;
        document.getElementById('csImg').value = '';
        openModal('modalCS');
    }

    //submit form 
    document.getElementById('formCS').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('csId').value;
        const fd = new FormData();
        fd.append('ten_co_so', document.getElementById('csName').value.trim());
        fd.append('dia_chi', document.getElementById('csAddr').value.trim());
        if (document.getElementById('csImg').files[0]) {
            fd.append('anh_bia', document.getElementById('csImg').files[0]);
        }

        let url = '../api/them_co_so.php';
        if (id) {
            fd.append('co_so_id', id);
            url = '../api/sua_co_so.php';
        }
        const res = await fetch(url, { method: 'POST', body: fd });
        const result = await res.json();
        if (result.success) {
            showToast(result.message);
            closeModal('modalCS');
            loadFacilities();
        } else {
            showToast(result.error, false);
        }
    });

    function confirmXoaCS(id, name) {
        document.getElementById('xoaMessage').innerHTML = `Xóa cơ sở <strong>${name}</strong>?<br><small style="color:#e76f51;">Tất cả khu vực và sân cũng sẽ bị xóa.</small>`;
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('co_so_id', id);
            const res = await fetch('../api/xoa_co_so.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
                loadFacilities();
            } else {
                showToast(result.error, false);
            }
        };
        openModal('modalXoa');
    }

    document.getElementById('btnThemKV').addEventListener('click', () => {
        document.getElementById('modalKVTitle').textContent = 'Thêm Khu Vực';
        document.getElementById('kvSubmitBtn').textContent = 'Thêm';
        document.getElementById('kvId').value = '';
        document.getElementById('kvName').value = '';
        openModal('modalKV');
    });

    function openSuaKV(id, name) {
        document.getElementById('modalKVTitle').textContent = 'Sửa Khu Vực';
        document.getElementById('kvSubmitBtn').textContent = 'Cập Nhật';
        document.getElementById('kvId').value = id;
        document.getElementById('kvName').value = name;
        openModal('modalKV');
    }

    //submit form
    document.getElementById('formKV').addEventListener('submit', async (e) => {
        e.preventDefault();
        const kvId = document.getElementById('kvId').value;
        const fd = new FormData();
        fd.append('ten_kv', document.getElementById('kvName').value.trim());

        let url;
        if (kvId) {
            fd.append('khu_vuc_id', kvId);
            url = '../api/sua_khu_vuc.php';
        } else {
            fd.append('co_so_id', currentCoSoId);
            url = '../api/them_khu_vuc.php';
        }
        const res = await fetch(url, { method: 'POST', body: fd });
        const result = await res.json();
        if (result.success) {
            showToast(result.message);
            closeModal('modalKV');
            loadKhuVuc(currentCoSoId);
        } else {
            showToast(result.error, false);
        }
    });

    // xóa khu vực
    function confirmXoaKV(id, name) {
        document.getElementById('xoaMessage').innerHTML = `Xóa khu vực <strong>${name}</strong>?<br><small style="color:#e76f51;">Tất cả sân thuộc khu vực này cũng sẽ bị xóa.</small>`;
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('khu_vuc_id', id);
            const res = await fetch('../api/xoa_khu_vuc.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
                loadKhuVuc(currentCoSoId);
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