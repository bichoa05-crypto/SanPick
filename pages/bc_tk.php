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
                <a href="../pages/quanlydichvu.php">Quản Lý Dịch Vụ</a>
                <a href="../pages/qlkh.php">Quản Lý Khách Hàng</a>
                <a class="active">Báo Cáo Thống Kê</a>
            </nav>
            <div class="user-actions"></div>
        </header>

        <div class="location-bar">
            <div class="location-label">
                <i class="fas fa-map-pin"></i>
                <span>Cơ Sở:</span>
            </div>
            <div class="facility-tabs" id="facilityTabs"></div>
        </div>

        <div class="tk-filter-bar">
            <span class="toolbar-label"><i class="fas fa-chart-bar"></i>Thống Kê:</span>
            <select id="filterThang" class="filter-select">
                <option value="0">Cả năm</option>
                <option value="1">Tháng 1</option><option value="2">Tháng 2</option><option value="3">Tháng 3</option>
                <option value="4">Tháng 4</option><option value="5">Tháng 5</option><option value="6">Tháng 6</option>
                <option value="7">Tháng 7</option><option value="8">Tháng 8</option><option value="9">Tháng 9</option>
                <option value="10">Tháng 10</option><option value="11">Tháng 11</option><option value="12">Tháng 12</option>
            </select>
            <select id="filterNam" class="filter-select"></select>
        </div>

        <div class="tk-summary-grid">
            <div class="tk-summary-card">
                <div class="tk-summary-label"><i class="fas fa-wallet"></i>Doanh Thu</div>
                <div id="statDT" class="tk-summary-value">0đ</div>
            </div>
            <div class="tk-summary-card">
                <div class="tk-summary-label"><i class="fas fa-calendar-check"></i>Tổng Đơn</div>
                <div id="statDon" class="tk-summary-value">0</div>
            </div>
            <div class="tk-summary-card">
                <div class="tk-summary-label"><i class="fas fa-users"></i>Khách Hàng</div>
                <div id="statKH" class="tk-summary-value">0</div>
            </div>
            <div class="tk-summary-card">
                <div class="tk-summary-label"><i class="fas fa-check-circle"></i>Hoàn Thành</div>
                <div id="statHT" class="tk-summary-value">0</div>
            </div>
        </div>

        <!--trang thai don-->
        <div class="tk-status-grid">
            <div class="tk-status-box cho">
                <div class="tk-status-label cho">Chờ XN</div>
                <div id="stCho" class="tk-status-value cho">0</div>
            </div>
            <div class="tk-status-box xn">
                <div class="tk-status-label xn">Đã XN</div>
                <div id="stXN" class="tk-status-value xn">0</div>
            </div>
            <div class="tk-status-box ht">
                <div class="tk-status-label ht">Hoàn Thành</div>
                <div id="stHTT" class="tk-status-value ht">0</div>
            </div>
            <div class="tk-status-box huy">
                <div class="tk-status-label huy">Đã Hủy</div>
                <div id="stHuy" class="tk-status-value huy">0</div>
            </div>
        </div>

        <!-- Top sân + Top khách -->
        <div class="tk-two-col">
            <!-- Top sân -->
            <div class="tk-panel">
                <div class="tk-panel-title">
                    <i class="fas fa-trophy"></i>Top Sân Đặt Nhiều
                </div>
                <div id="topSanList"></div>
            </div>
            <!-- Top khách -->
            <div class="tk-panel">
                <div class="tk-panel-title">
                    <i class="fas fa-star"></i>Top Khách Hàng
                </div>
                <div id="topKhachList"></div>
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
            </div>`;
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault(); localStorage.removeItem('user'); window.location.href = 'dang_nhap.php';
        });
    }
    document.addEventListener('click', (e) => {
        const dd = document.querySelector('.user-dropdown');
        if (dd && dd.contains(e.target)) dd.querySelector('.dropdown-menu').style.display = 'block';
        else { const m = document.querySelector('.dropdown-menu'); if (m) m.style.display = 'none'; }
    });

    const curYear = new Date().getFullYear();
    const curMonth = new Date().getMonth() + 1;
    document.getElementById('filterThang').value = curMonth;
    const ys = document.getElementById('filterNam');
    for (let y = curYear; y >= curYear - 3; y--) { const o = document.createElement('option'); o.value = y; o.textContent = y; ys.appendChild(o); }
    document.getElementById('filterThang').addEventListener('change', loadStats);
    document.getElementById('filterNam').addEventListener('change', loadStats);

    //tab cơ sở
    function getQueryParam(p) { return new URLSearchParams(window.location.search).get(p); }
    async function loadFacilities() {
        const res = await fetch('../api/get_co_so.php');
        const result = await res.json();
        if (!result.success || result.data.length === 0) return;
        const css = result.data;
        const urlId = getQueryParam('co_so_id');
        let targetId = css[0].co_so_id;
        if (urlId && css.some(c => c.co_so_id == urlId)) targetId = urlId;
        const tabs = document.getElementById('facilityTabs');
        css.forEach(cs => {
            const btn = document.createElement('button');
            btn.className = 'facility-btn' + (cs.co_so_id == targetId ? ' active' : '');
            btn.dataset.coSoId = cs.co_so_id;
            btn.textContent = cs.ten_co_so;
            tabs.appendChild(btn);
        });
        tabs.addEventListener('click', (e) => {
            if (!e.target.classList.contains('facility-btn')) return;
            tabs.querySelectorAll('.facility-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            currentCoSoId = e.target.dataset.coSoId;
            loadStats();
        });
        currentCoSoId = targetId;
        loadStats();
    }

    function fmtPrice(v) { return Number(v || 0).toLocaleString('vi-VN') + 'đ'; }

    // load thống kê
    async function loadStats() {
        if (!currentCoSoId) return;
        const thang = document.getElementById('filterThang').value;
        const nam = document.getElementById('filterNam').value;
        const res = await fetch(`../api/thong_ke_api.php?co_so_id=${currentCoSoId}&thang=${thang}&nam=${nam}`);
        const result = await res.json();
        if (!result.success) return;
        const tq = result.data.tong_quan;

        document.getElementById('statDT').textContent = fmtPrice(tq.tong_doanh_thu);
        document.getElementById('statDon').textContent = tq.tong_don || 0;
        document.getElementById('statKH').textContent = tq.tong_khach || 0;
        document.getElementById('statHT').textContent = tq.hoan_thanh || 0;
        document.getElementById('stCho').textContent = tq.cho_xac_nhan || 0;
        document.getElementById('stXN').textContent = tq.da_xac_nhan || 0;
        document.getElementById('stHTT').textContent = tq.hoan_thanh || 0;
        document.getElementById('stHuy').textContent = tq.da_huy || 0;

        // top sân
        const sanC = document.getElementById('topSanList');
        const ts = result.data.top_san;
        if (!ts || ts.length === 0) { sanC.innerHTML = '<span class="empty-text">Chưa có dữ liệu</span>'; }
        else {
            sanC.innerHTML = ts.map((s, i) => `
                <div class="tk-list-row">
                    <div>
                        <span class="tk-rank-name">${i + 1}. ${s.ten_san}</span>
                        <span class="tk-rank-sub">${s.ten_kv}</span>
                    </div>
                    <div style="text-align:right;">
                        <span class="tk-rank-val">${s.so_luot} lượt</span>
                        <span class="tk-rank-extra">${fmtPrice(s.doanh_thu)}</span>
                    </div>
                </div>
            `).join('');
        }

        // top khách
        const khC = document.getElementById('topKhachList');
        const tk = result.data.top_khach;
        if (!tk || tk.length === 0) { khC.innerHTML = '<span class="empty-text">Chưa có dữ liệu</span>'; }
        else {
            khC.innerHTML = tk.map((k, i) => `
                <div class="tk-list-row">
                    <div>
                        <span class="tk-rank-name">${i + 1}. ${k.ho_ten}</span>
                        <span class="tk-rank-sub">${k.sdt || ''}</span>
                    </div>
                    <div style="text-align:right;">
                        <span class="tk-rank-val">${k.so_don} đơn</span>
                        <span class="tk-rank-extra">${fmtPrice(k.tong_chi)}</span>
                    </div>
                </div>
            `).join('');
        }
    }

    loadFacilities();
    </script>
</body>
</html>