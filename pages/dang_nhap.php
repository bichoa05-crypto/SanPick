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
            <!-- Thay đường dẫn logo phù hợp -->
            <img src="../assets/img/logo.png" alt="PickleBall UTT Logo" onerror="this.style.display='none'">
            <h2>PickleBall UTT</h2>
            <p>Chuỗi sân PickleBall chuyên nghiệp</p>
        </div>

        <form class="login-form" id="loginForm">
            <div class="input-group">
                <label >Email hoặc Số điện thoại</label>
                <div class="input-field">
                    <i class="far fa-envelope"></i>
                    <input type="text" id="username" name="username" value="" placeholder="Nhập email hoặc số điện thoại" required>
                </div>
            </div>

            <div class="input-group">
                <label>Mật khẩu</label>
                <div class="input-field">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <i class="fas fa-eye-slash input-icon-right" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="login-btn">Đăng nhập</button>
        </form>
        <div class="signup-prompt">
            Chưa có tài khoản? <a href="dang_ky.php">Đăng ký ngay</a>
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

        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            });
        
        //submit form đăng nhập
        document.getElementById('loginForm').addEventListener('submit', async (e
) => {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                showToast('Vui lòng nhập đầy đủ thông tin', false);
                return;
            }

            try {
                const response = await fetch('../api/dang_nhap_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const result = await response.json();

                if (result.success) {
                    // Đăng nhập thành công, lưu thông tin user vào localStorage
                    localStorage.setItem('user', JSON.stringify(result.data));
                    showToast('Đăng nhập thành công!');
                    setTimeout(() => {
                        if (result.data.vai_tro === 'chu_san') {
                            window.location.href = 'dsdatlich.php';
                        } else {
                            window.location.href = '../index.php';
                        }
                    }, 1200);
                } else {
                    showToast(result.error || 'Đăng nhập thất bại', false);
                }
            } catch (error) {
                console.error('Lỗi khi đăng nhập:', error);
                showToast('Có lỗi xảy ra. Vui lòng thử lại sau.', false);
            }
        });
    </script>
</body>
</html>