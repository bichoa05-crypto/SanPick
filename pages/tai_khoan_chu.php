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
    <a href="dsdatlich.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
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
                <div class="profile-name" id="sidebarName"></div>
            </div>

            <ul class="account-menu">
                <li><a href="#" class="active" data-tab="info"><i class="far fa-user-circle"></i> Thông Tin Cá Nhân</a></li>
                <li><a href="#" data-tab="password"><i class="fas fa-lock"></i> Đổi Mật Khẩu</a></li>
            </ul>

            <div class="logout-link">
                <a href="#" id="logoutSidebarBtn"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="account-content" id="accountContent">
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
        const defaultAvatar = '../assets/img/default-avatar.png';

        function showToast(msg, ok = true) {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
            t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
            document.body.appendChild(t);
            setTimeout(() => t.style.transform = 'translateX(0)', 10);
            setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
        }
        if (user && user.vai_tro === 'khach_hang') {
            window.location.href = '../index.php';
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
            window.location.href = 'dang_nhap.php';
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
            formData.append('avatar', user.avatar || '');

            const avatarFile = document.getElementById('avatarInput').files[0];
            if (avatarFile) {
                formData.append('avatar_file', avatarFile);
            }

            fetch('../api/up_user.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const updatedUser = data.data;
                    localStorage.setItem('user', JSON.stringify(updatedUser));

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
        const menuLinks = document.querySelectorAll('.account-menu a[data-tab]');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.dataset.tab;

                menuLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                const contentDiv = document.getElementById('accountContent');
                const headerTitle = contentDiv.querySelector('.content-header h1');
                const headerDesc = contentDiv.querySelector('.content-header p');
                const infoCard = contentDiv.querySelector('.info-card');

                if (tab === 'info') {
                    headerTitle.textContent = 'Thông Tin Cá Nhân';
                    headerDesc.textContent = 'Cập nhật thông tin của bạn';
                    infoCard.innerHTML = `
                        <form id="profileForm">
                            <div class="info-row">
                                <span class="info-label"><i class="far fa-user" style="margin-right: 8px;"></i>Họ Tên</span>
                                <input type="text" class="info-input" id="fullnameInput" value="${user?.ho_ten || ''}">
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="far fa-envelope" style="margin-right: 8px;"></i>Email</span>
                                <input type="email" class="info-input" id="emailInput" value="${user?.email || ''}">
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-phone-alt" style="margin-right: 8px;"></i>SĐT</span>
                                <input type="tel" class="info-input" id="phoneInput" value="${user?.sdt || ''}">
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
                }
            });
        });
    })();
    </script>
</body>

</html>
