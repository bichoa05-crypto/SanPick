<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dangnhap.css">
</head>
<body class="login-background">
    <div class="login-container">
        <div class="logo-area">
            <img src="../assets/img/logo.png" alt="PickleBall UTT Logo" onerror="this.style.display='none'">
            <h2>PickleBall UTT</h2>
            <p>Tạo tài khoản mới</p>
        </div>

        <form class="login-form" id="registerForm">
            <div class="input-group">
                <label>Họ và tên</label>
                <div class="input-field">
                    <i class="far fa-user"></i>
                    <input type="text" id="hoTen" name="ho_ten" placeholder="Nhập họ và tên" required>
                </div>
            </div>

            <div class="input-group">
                <label>Email</label>
                <div class="input-field">
                    <i class="far fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Nhập email" required>
                </div>
            </div>

            <div class="input-group">
                <label>Số điện thoại</label>
                <div class="input-field">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="sdt" name="sdt" placeholder="Nhập số điện thoại" required>
                </div>
            </div>

            <div class="input-group">
                <label>Mật khẩu</label>
                <div class="input-field">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <i class="fas fa-eye-slash input-icon-right" id="togglePassword"></i>
                </div>
            </div>

            <div class="input-group">
                <label>Xác nhận mật khẩu</label>
                <div class="input-field">
                    <input type="password" id="confirmPassword" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                    <i class="fas fa-eye-slash input-icon-right" id="toggleConfirmPassword"></i>
                </div>
            </div>

            <button type="submit" class="login-btn">Đăng ký</button>
        </form>
        <div class="signup-prompt">
            Đã có tài khoản? <a href="dang_nhap.php">Đăng nhập</a>
        </div>
    </div>

    <script>
        function showToast(msg, ok = true) {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
            t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
            document.body.appendChild(t);
            setTimeout(() => t.style.transform = 'translateX(0)', 10);
            setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
        }

        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirmPassword');
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // submit form đăng ký
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const hoTen = document.getElementById('hoTen').value.trim();
            const email = document.getElementById('email').value.trim();
            const sdt = document.getElementById('sdt').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirmPassword').value.trim();

            if (!hoTen || !email || !sdt || !password || !confirmPassword) {
                showToast('Vui lòng nhập đầy đủ thông tin', false);
                return;
            }

            if (password.length < 6) {
                showToast('Mật khẩu phải có ít nhất 6 ký tự', false);
                return;
            }

            if (password !== confirmPassword) {
                showToast('Mật khẩu xác nhận không khớp', false);
                return;
            }

            const phoneRegex = /^0\d{9}$/;
            if (!phoneRegex.test(sdt)) {
                showToast('Số điện thoại không hợp lệ', false);
                return;
            }

            try {
                const response = await fetch('../api/dang_ky_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ho_ten: hoTen, email, sdt, mat_khau: password })
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Đăng ký thành công! Vui lòng đăng nhập.');
                    setTimeout(() => { window.location.href = 'dang_nhap.php'; }, 1200);
                } else {
                    showToast(result.error || 'Đăng ký thất bại', false);
                }
            } catch (error) {
                console.error('Lỗi khi đăng ký:', error);
                showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', false);
            }
        });
    </script>
</body>
</html>
