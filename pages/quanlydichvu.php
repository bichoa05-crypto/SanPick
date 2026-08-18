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
                <a href="../pages/ql_khung_gio.php">Quản Lý Khung Giờ</a>
                <a class="active">Quản Lý Dịch Vụ</a>
                <a href="../pages/qlkh.php">Quản Lý Khách Hàng</a>
                <a href="../pages/bc_tk.php">Báo Cáo Thống Kê</a>
            </nav>
            <div class="user-actions"></div>
        </header>

        <!-- Toolbar: lọc loại + thêm -->
        <div class="toolbar">
            <div class="toolbar-left">
                <span class="toolbar-label">
                    <i class="fas fa-concierge-bell"></i>Dịch Vụ:
                </span>
                <button class="filter-loai filter-btn active" data-filter="all">Tất cả</button>
                <button class="filter-loai filter-btn" data-filter="thue_vot">
                    <i class="fas fa-table-tennis-paddle-ball"></i>Thuê Vợt
                </button>
                <button class="filter-loai filter-btn" data-filter="mua_bong">
                    <i class="fas fa-basketball-ball"></i>Mua Bóng
                </button>
                <button class="filter-loai filter-btn" data-filter="khac">
                    <i class="fas fa-ellipsis"></i>Khác
                </button>
            </div>
            <button id="btnThemDV" class="btn-add-sm">
                <i class="fas fa-plus"></i> Thêm Dịch Vụ
            </button>
        </div>

        <!-- Danh sách dịch vụ -->
        <div id="dichVuList" class="card-grid"></div>
    </div>

    <!-- Modal thêm/sửa dịch vụ -->
    <div id="modalDV" class="modal-overlay">
        <div class="modal-box">
            <h3 id="modalDVTitle" class="modal-title">Thêm Dịch Vụ</h3>
            <form id="formDV">
                <input type="hidden" id="dvId">
                <div class="form-group">
                    <label>Tên Dịch Vụ</label>
                    <input type="text" id="dvTen" required>
                </div>
                <div class="form-group">
                    <label>Loại Dịch Vụ</label>
                    <select id="dvLoai" required>
                        <option value="thue_vot">Thuê Vợt</option>
                        <option value="mua_bong">Mua Bóng</option>
                        <option value="khac">Khác</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Đơn Giá (VNĐ)</label>
                        <input type="number" id="dvGia" min="0" step="1000" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Đơn Vị</label>
                        <input type="text" id="dvDonVi" >
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô Tả</label>
                    <textarea id="dvMoTa" rows="2"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="closeModal('modalDV')">Hủy</button>
                    <button type="submit" class="btn-primary" id="dvSubmitBtn">Thêm</button>
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
    let allDVData = [];
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

    //loại dịch vụ
    const loaiMap = {
        'thue_vot': { label: 'Thuê vợt', icon: 'fa-table-tennis-paddle-ball', color: '#0a3b3b', bg: '#f0f5f3' },
        'mua_bong': { label: 'Mua bóng', icon: 'fa-basketball-ball', color: '#0a3b3b', bg: '#f0f5f3' },
        'khac': { label: 'Khác', icon: 'fa-box', color: '#0a3b3b', bg: '#f0f5f3' }
    };

    function formatPrice(p) {
        return Number(p).toLocaleString('vi-VN') + 'đ';
    }

    async function loadDichVu() {
        const res = await fetch('../api/get_dich_vu.php');
        const result = await res.json();
        allDVData = result.success ? result.data : [];
        renderDichVu();
    }

    function renderDichVu() {
        const container = document.getElementById('dichVuList');
        let data = allDVData;
        if (currentFilter !== 'all') {
            data = data.filter(dv => dv.loai_dich_vu === currentFilter);
        }
        if (data.length === 0) {
            container.innerHTML = '<span class="empty-text-lg">Không có dịch vụ nào</span>';
            return;
        }
        container.innerHTML = data.map(dv => {
            const loai = loaiMap[dv.loai_dich_vu] || loaiMap['khac'];
            const tenEsc = dv.ten_dich_vu.replace(/'/g, "\\'");
            const donViEsc = (dv.don_vi || '').replace(/'/g, "\\'");
            const moTaEsc = (dv.mo_ta || '').replace(/'/g, "\\'");
            return `
            <div class="card card-fixed card-col">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas ${loai.icon}"></i>${dv.ten_dich_vu}
                    </div>
                    <span class="badge-loai">
                        ${loai.label}
                    </span>
                </div>
                <div class="card-price">
                    <i class="fas fa-tag"></i>${formatPrice(dv.don_gia)}${dv.don_vi ? ` / ${dv.don_vi}` : ''}
                </div>
                ${dv.mo_ta ? `<div class="card-subtitle">${dv.mo_ta}</div>` : ''}
                <div class="card-spacer"></div>
                <div class="card-actions">
                    <button class="btn-facility-edit" onclick="openSuaDV(${dv.dich_vu_id}, '${tenEsc}', '${dv.loai_dich_vu}', ${dv.don_gia}, '${donViEsc}', '${moTaEsc}')">
                        <i class="fas fa-pen"></i> Sửa
                    </button>
                    <button class="btn-facility-delete" onclick="confirmXoaDV(${dv.dich_vu_id}, '${tenEsc}')">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
            `;
        }).join('');
    }

    //lọc theo loại dịch vụ
    document.querySelectorAll('.filter-loai').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-loai').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderDichVu();
        });
    });

    document.getElementById('btnThemDV').addEventListener('click', () => {
        document.getElementById('modalDVTitle').textContent = 'Thêm Dịch Vụ';
        document.getElementById('dvSubmitBtn').textContent = 'Thêm';
        document.getElementById('dvId').value = '';
        document.getElementById('dvTen').value = '';
        document.getElementById('dvLoai').value = 'thue_vot';
        document.getElementById('dvGia').value = '';
        document.getElementById('dvDonVi').value = '';
        document.getElementById('dvMoTa').value = '';
        openModal('modalDV');
    });

    function openSuaDV(id, ten, loai, gia, donVi, moTa) {
        document.getElementById('modalDVTitle').textContent = 'Sửa Dịch Vụ';
        document.getElementById('dvSubmitBtn').textContent = 'Cập Nhật';
        document.getElementById('dvId').value = id;
        document.getElementById('dvTen').value = ten;
        document.getElementById('dvLoai').value = loai;
        document.getElementById('dvGia').value = gia;
        document.getElementById('dvDonVi').value = donVi;
        document.getElementById('dvMoTa').value = moTa;
        openModal('modalDV');
    }

    document.getElementById('formDV').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('dvId').value;
        const fd = new FormData();
        fd.append('ten_dich_vu', document.getElementById('dvTen').value.trim());
        fd.append('loai_dich_vu', document.getElementById('dvLoai').value);
        fd.append('don_gia', document.getElementById('dvGia').value);
        fd.append('don_vi', document.getElementById('dvDonVi').value.trim());
        fd.append('mo_ta', document.getElementById('dvMoTa').value.trim());

        let url;
        if (id) {
            fd.append('dich_vu_id', id);
            url = '../api/sua_dich_vu.php';
        } else {
            url = '../api/them_dich_vu.php';
        }
        const res = await fetch(url, { method: 'POST', body: fd });
        const result = await res.json();
        if (result.success) {
            showToast(result.message);
            closeModal('modalDV');
            loadDichVu();
        } else {
            showToast(result.error, false);
        }
    });

    function confirmXoaDV(id, name) {
        document.getElementById('xoaMessage').innerHTML = `Xóa dịch vụ <strong>${name}</strong>?`;
        document.getElementById('btnXacNhanXoa').onclick = async () => {
            const fd = new FormData();
            fd.append('dich_vu_id', id);
            const res = await fetch('../api/xoa_dich_vu.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                showToast(result.message);
                closeModal('modalXoa');
                loadDichVu();
            } else {
                showToast(result.error, false);
            }
        };
        openModal('modalXoa');
    }

    loadDichVu();
    </script>
</body>
</html>