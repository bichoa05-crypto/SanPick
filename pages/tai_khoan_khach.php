<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tk.css">
</head>

<body>
    <div class="container">
    <a href="../index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <header class="header">
        <div class="logo">
            <img src="../assets/img/logo.png" style="width: 80px; height: 80px; margin-right: 8px;">
            <span>PickleBall UTT</span>
        </div>
    </header>
    </div>
    <div class="account-container">
        <!-- SIDEBAR -->
        <div class="account-sidebar">
            <div class="profile-header">
                <img src="../assets/img/default-avatar.png" alt="Avatar" class="profile-avatar" id="sidebarAvatar">
                <div class="profile-name" id="sidebarName">Nguyễn Mạnh Quỳnh</div>
            </div>

            <ul class="account-menu">
                <li><a href="#" class="active" data-tab="info"><i class="far fa-user-circle"></i> Thông Tin Cá Nhân</a></li>
                <li><a href="#" data-tab="password"><i class="fas fa-lock" ></i> Đổi Mật Khẩu</a></li>
                <li><a href="#" data-tab="history"><i class="far fa-calendar-alt"></i> Lịch Sử Đặt Sân</a></li>
            </ul>

            <div class="logout-link">
                <a href="#" id="logoutSidebarBtn"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="account-content" id="accountContent">
            <!-- Nội dung sẽ được load bằng JS (tab) hoặc mặc định hiển thị thông tin cá nhân -->
            <div class="content-header">
                <h1>Thông Tin Cá Nhân</h1>
                <p>Cập nhật thông tin của bạn</p>
            </div>

            <div class="info-card">
                <form id="profileForm">
                    <div class="info-row">
                        <span class="info-label"><i class="far fa-user" style="margin-right: 8px;"></i>Họ Tên</span>
                        <input type="text" class="info-input" id="fullnameInput" value="">
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="far fa-envelope" style="margin-right: 8px;"></i>Email</span>
                        <input type="email" class="info-input" id="emailInput" value="">
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone-alt" style="margin-right: 8px;"></i>SĐT</span>
                        <input type="tel" class="info-input" id="phoneInput" value="">
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-image" style="margin-right: 8px;"></i>Ảnh đại diện</span>
                        <input type="file" class="info-input" id="avatarInput" accept="image/*">
                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="save-btn" id="saveProfileBtn">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const user = JSON.parse(localStorage.getItem('user'));
        if (user && user.vai_tro === 'chu_san') {
            window.location.href = 'dsdatlich.php';
        }        
        const defaultAvatar = '../assets/img/default-avatar.png';

        function showToast(msg, ok = true) {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
            t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
            document.body.appendChild(t);
            setTimeout(() => t.style.transform = 'translateX(0)', 10);
            setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
        }

        if (user) {
            document.getElementById('sidebarName').textContent = user.ho_ten || '';
            document.getElementById('sidebarAvatar').src = '../' + user.avatar || defaultAvatar;
            if (user.ho_ten) document.getElementById('fullnameInput').value = user.ho_ten;
            if (user.email) document.getElementById('emailInput').value = user.email;
            if (user.sdt) document.getElementById('phoneInput').value = user.sdt;
        }

        // Xử lý đăng xuất
        function handleLogout(e) {
            e.preventDefault();
            localStorage.removeItem('user');
            window.location.href = '../index.php';
        }

        document.getElementById('logoutSidebarBtn').addEventListener('click', handleLogout);

        // Khi submit form
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('user_id', user.user_id);
            formData.append('ho_ten', document.getElementById('fullnameInput').value);
            formData.append('email', document.getElementById('emailInput').value);
            formData.append('sdt', document.getElementById('phoneInput').value);
            formData.append('avatar', user.avatar || ''); // avatar cũ

            const avatarFile = document.getElementById('avatarInput').files[0];
            if (avatarFile) {
                formData.append('avatar_file', avatarFile);
            }

            // Gửi API cập nhật lên server
            fetch('../api/up_user.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật localStorage với dữ liệu mới từ server
                    const updatedUser = data.data;
                    localStorage.setItem('user', JSON.stringify(updatedUser));

                    // Cập nhật giao diện
                    document.getElementById('sidebarName').textContent = updatedUser.ho_ten;
                    if (updatedUser.avatar) {
                        document.getElementById('sidebarAvatar').src = '../' + updatedUser.avatar;
                    }

                    showToast('Thông tin đã được cập nhật!');
                    localStorage.setItem('user', JSON.stringify(updatedUser));
                } else {
                    showToast('Lỗi: ' + data.error, false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Có lỗi xảy ra khi cập nhật!', false);
            });
        });

        // Xử lý tab
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab') || 'info';
        const menuLinks = document.querySelectorAll('.account-menu a[data-tab]');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.dataset.tab;

                // Active menu
                menuLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Thay đổi nội dung chính dựa trên tab
                const contentDiv = document.getElementById('accountContent');
                const headerTitle = contentDiv.querySelector('.content-header h1');
                const headerDesc = contentDiv.querySelector('.content-header p');
                const infoCard = contentDiv.querySelector('.info-card');

                if (tab === 'info') {
                    headerTitle.textContent = 'Thông Tin Cá Nhân';
                    headerDesc.textContent = 'Cập nhật thông tin của bạn';
                    // Hiển thị lại form thông tin (đã có sẵn)
                    infoCard.innerHTML = `
                        <form id="profileForm">
                            <div class="info-row">
                                <span class="info-label"><i class="far fa-user" style="margin-right: 8px;"></i>Họ Tên</span>
                                <input type="text" class="info-input" id="fullnameInput" value="${user?.ho_ten || ''}" >
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="far fa-envelope" style="margin-right: 8px;"></i>Email</span>
                                <input type="email" class="info-input" id="emailInput" value="${user?.email || ''}" >
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-phone-alt" style="margin-right: 8px;"></i>SĐT</span>
                                <input type="tel" class="info-input" id="phoneInput" value="${user?.sdt || ''}" >
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-image" style="margin-right: 8px;"></i>Ảnh đại diện</span>
                                <input type="file" class="info-input" id="avatarInput" accept="image/*">
                            </div>
                            <div class="action-buttons">
                                <button type="submit" class="save-btn" id="saveProfileBtn">Lưu thay đổi</button>
                            </div>
                        </form>
                    `;
                } else if (tab === 'password') {
                    headerTitle.textContent = 'Đổi Mật Khẩu';
                    headerDesc.textContent = 'Bảo mật tài khoản của bạn';
                    infoCard.innerHTML = `
                        <form id="passwordForm">
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-lock"></i> Mật khẩu hiện tại</span>
                                <input type="password" class="info-input" id="currentPassword" placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-key"></i> Mật khẩu mới</span>
                                <input type="password" class="info-input" id="newPassword" placeholder="Ít nhất 8 ký tự">
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-check-circle"></i> Xác nhận mật khẩu</span>
                                <input type="password" class="info-input" id="confirmPassword" placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div class="action-buttons">
                                <button type="submit" class="save-btn">Cập nhật mật khẩu</button>
                            </div>
                        </form>
                    `;
                    document.getElementById('passwordForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const cur = document.getElementById('currentPassword').value;
                        const newP = document.getElementById('newPassword').value;
                        const conf = document.getElementById('confirmPassword').value;
                        if (newP !== conf) {
                            showToast('Mật khẩu mới không khớp!', false);
                            return;
                        }
                        // Gọi API đổi mật khẩu
                        fetch('../api/up_mk_api.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                user_id: user.user_id,
                                new_password: newP
                            })
                        }).then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showToast('Mật khẩu đã được cập nhật!');
                                document.getElementById('currentPassword').value = '';
                                document.getElementById('newPassword').value = '';
                                document.getElementById('confirmPassword').value = '';
                            } else {
                                showToast('Lỗi: ' + data.error, false);
                            }
                        }).catch(err => {
                            console.error(err);
                            showToast('Có lỗi xảy ra khi cập nhật mật khẩu!', false);
                        });

                    });
                } else if(tab === 'history'){
                    headerTitle.textContent = 'Lịch Sử Đặt Sân';
                    headerDesc.textContent = 'Danh sách sân đã đặt';
                    infoCard.innerHTML = `
                        <div class="history-filter">
                            <div class="filter-group">
                                <label><i class="far fa-calendar"></i> Từ ngày</label>
                                <input type="date" id="filterFromDate" class="filter-input">
                            </div>
                            <div class="filter-group">
                                <label><i class="far fa-calendar"></i> Đến ngày</label>
                                <input type="date" id="filterToDate" class="filter-input">
                            </div>
                            <div class="filter-group">
                                <label><i class="fas fa-filter"></i> Trạng Thái</label>
                                <select id="filterStatus" class="filter-input">
                                    <option value="">Tất cả</option>
                                    <option value="cho_xac_nhan">Chờ Xác Nhận</option>
                                    <option value="da_xac_nhan">Đã Xác Nhận</option>
                                    <option value="hoan_thanh">Hoàn Thành</option>
                                    <option value="da_huy">Đã Hủy</option>
                                </select>
                            </div>
                            <button class="filter-btn" id="filterBtn"><i class="fas fa-search"></i> Lọc</button>
                            <button class="filter-reset-btn" id="filterResetBtn"><i class="fas fa-redo"></i> Tất cả</button>
                        </div>
                        <div id="historyContent"><div class="history-loading"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div></div>
                    `;

                    let allHistoryData = [];

                    function renderHistory(items) {
                        const container = document.getElementById('historyContent');
                        if (!items || !items.length) {
                            container.innerHTML = '<div class="history-empty"><i class="far fa-calendar-times"></i><p>Không có đơn đặt sân nào</p></div>';
                            return;
                        }

                        let html = '<div class="history-list">';
                        items.forEach(item => {
                            const trangThaiMap = {
                                'cho_xac_nhan': { text: 'Chờ Xác Nhận', cls: 'status-pending' },
                                'da_xac_nhan': { text: 'Đã Xác Nhận', cls: 'status-confirmed' },
                                'hoan_thanh': { text: 'Hoàn Thành', cls: 'status-paid' },
                                'da_huy': { text: 'Đã Hủy', cls: 'status-cancelled' }
                            };
                            const status = trangThaiMap[item.trang_thai] || { text: item.trang_thai, cls: '' };

                            let dvHtml = '';
                            if (item.dich_vu && item.dich_vu.length) {
                                dvHtml = item.dich_vu.map(dv => 
                                    `<span class="dv-tag">${dv.ten_dich_vu} x${dv.so_luong} (${Number(dv.thanh_tien).toLocaleString('vi-VN')}đ)</span>`
                                ).join('');
                            } else {
                                dvHtml = '<span class="dv-tag dv-none">Không có</span>';
                            }

                            const cancelBtn = item.trang_thai === 'cho_xac_nhan'
                                ? `<button class="cancel-btn" data-id="${item.dat_san_id}"><i class="fas fa-times-circle"></i> Hủy đơn</button>`
                                : '';

                            html += `
                            <div class="history-card">
                                <div class="history-card-header">
                                    <div class="history-co-so"><i class="fas fa-building"></i> ${item.ten_co_so}</div>
                                    <span class="history-status ${status.cls}">${status.text}</span>
                                </div>
                                <div class="history-card-body">
                                    <div class="history-detail">
                                        <div class="history-detail-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><strong>Khu vực:</strong> ${item.ten_kv}</span>
                                        </div>
                                        <div class="history-detail-item">
                                            <i class="fas fa-table-tennis"></i>
                                            <span><strong>Sân:</strong> ${item.ten_san}</span>
                                        </div>
                                        <div class="history-detail-item">
                                            <i class="far fa-clock"></i>
                                            <span><strong>Khung giờ:</strong> ${item.gio_bat_dau.substring(0,5)} - ${item.gio_ket_thuc.substring(0,5)}</span>
                                        </div>
                                        <div class="history-detail-item">
                                            <i class="far fa-calendar"></i>
                                            <span><strong>Ngày:</strong> ${item.ngay_dat}</span>
                                        </div>
                                    </div>
                                    <div class="history-services">
                                        <strong>Dịch vụ:</strong>
                                        <div class="dv-tags">${dvHtml}</div>
                                    </div>
                                </div>
                                <div class="history-card-footer">
                                    <div class="history-price">
                                        <span class="price-total"><i class="fas fa-money-bill-wave"></i> Tổng: <strong>${Number(item.tong_hoa_don).toLocaleString('vi-VN')}đ</strong></span>
                                    </div>
                                    <div class="history-meta">
                                        <span class="history-date"><i class="far fa-clock"></i> Ngày tạo: ${new Date(item.ngay_tao).toLocaleString('vi-VN')}</span>
                                        ${cancelBtn}
                                    </div>
                                </div>
                            </div>`;
                        });
                        html += '</div>';
                        container.innerHTML = html;

                        // Gắn sự kiện hủy đơn
                        container.querySelectorAll('.cancel-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const datSanId = this.dataset.id;
                                if (!confirm('Bạn có chắc muốn hủy đơn đặt sân này?')) return;

                                fetch('../api/huy_dat_san.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ dat_san_id: datSanId, user_id: user.user_id })
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) {
                                        showToast('Đã hủy đơn thành công!');
                                        document.querySelector('.account-menu a[data-tab="history"]').click();
                                    } else {
                                        showToast('Lỗi: ' + result.error, false);
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    showToast('Có lỗi xảy ra!', false);
                                });
                            });
                        });
                    }

                    // Lọc theo ngày + trạng thái
                    document.getElementById('filterBtn').addEventListener('click', function() {
                        const from = document.getElementById('filterFromDate').value;
                        const to = document.getElementById('filterToDate').value;
                        const status = document.getElementById('filterStatus').value;
                        let filtered = allHistoryData;
                        if (from) filtered = filtered.filter(i => i.ngay_dat >= from);
                        if (to) filtered = filtered.filter(i => i.ngay_dat <= to);
                        if (status) filtered = filtered.filter(i => i.trang_thai === status);
                        renderHistory(filtered);
                    });

                    document.getElementById('filterResetBtn').addEventListener('click', function() {
                        document.getElementById('filterFromDate').value = '';
                        document.getElementById('filterToDate').value = '';
                        document.getElementById('filterStatus').value = '';
                        renderHistory(allHistoryData);
                    });

                    // Load dữ liệu
                    fetch('../api/get_lich_su_dat.php?user_id=' + user.user_id)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success || !data.data.length) {
                            document.getElementById('historyContent').innerHTML = '<div class="history-empty"><i class="far fa-calendar-times"></i><p>Chưa có lịch sử đặt sân</p></div>';
                            return;
                        }
                        allHistoryData = data.data;
                        renderHistory(allHistoryData);
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('historyContent').innerHTML = '<div class="history-empty"><p>Có lỗi khi tải dữ liệu</p></div>';
                    });
                }
            });
        });

        if (initialTab !== 'info') {
            document.querySelector(`.account-menu a[data-tab="${initialTab}"]`).click();
        }
    })();
    
    </script>
</body>

</html>