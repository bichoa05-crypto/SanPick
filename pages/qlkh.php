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
                <a href="../pages/quanlydichvu.php">Quản Lý Dịch Vụ </a>
                <a class="active">Quản Lý Khách Hàng </a>
                <a href="../pages/bc_tk.php">Báo Cáo Thống Kê </a>
            </nav>
            <div class="user-actions">
            </div>
        </header>
        <!-- Customer Management Section -->
        <div class="management-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-users"></i> Quản Lý Khách Hàng
                </div>
                <div class="search-bar">
                    <input type="text" class="search-input" id="searchInput" placeholder="Tìm kiếm khách hàng..."
                        oninput="loadCustomers(this.value)">
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Thêm khách hàng
                    </button>
                </div>
            </div>

            <div class="customer-table">
                <table id="customerTable">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <!-- Customer data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <form id="editForm">
                <input type="hidden" id="editUserId">
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" id="editHoTen">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="editEmail">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" id="editSdt">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-outline" onclick="closeEditModal()">Hủy</button>
                    <button type="submit" class="btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Thêm khách hàng mới</h3>
            <form id="addForm">
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" id="addHoTen" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="addEmail">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" id="addSdt" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" id="addMatKhau" required>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-outline" onclick="closeAddModal()">Hủy</button>
                    <button type="submit" class="btn-primary">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const user = JSON.parse(localStorage.getItem('user'));

    function showToast(msg, ok = true) {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 10);
        setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    //xử lý đăng nhập hiển thị thông tin user ở header nếu đã đăng nhập
    const userActions = document.querySelector('.user-actions');
    if (user && user.vai_tro === 'khach_hang') {
        window.location.href = '../index.php';
    }    
    if (user) {
        userActions.innerHTML = `
                <div class="user-profile">
                    <img src="../${user.avatar || 'assets/img/default-avatar.png'}" 
                         alt="${user.ten_hien_thi}" 
                         class="user-avatar"
                         onerror="this.src='../assets/img/default-avatar.png'">
                    <span class="user-name">${user.ho_ten}</span>
                    <div class="user-dropdown">
                        <button class="dropdown-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
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
            if (menu) {
                menu.style.display = 'none';
            }
        }
    });

    async function loadCustomers(search = '') {
        try {
            const response = await fetch(`../api/khachhang.php?search=${encodeURIComponent(search)}`);

            const text = await response.text();
            try {
                const customers = JSON.parse(text);
                const tbody = document.getElementById('customerTableBody');

                if (!customers || customers.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" style="text-align:center;">Không tìm thấy khách hàng nào</td></tr>`;
                    return;
                }

                // Đổ dữ liệu vào bảng
                tbody.innerHTML = customers.map(user => `
                <tr>
                    <td><strong>${user.ho_ten}</strong></td>
                    <td>${user.email}</td>
                    <td>${user.sdt}</td>
                    <td>${new Date(user.ngay_tao).toLocaleDateString('vi-VN')}</td>
                    <td>
                        <button class="btn-facility-edit" onclick="editCustomer(${user.user_id})"><i class="fas fa-pen"></i> Sửa</button>
                        <button class="btn-facility-delete" onclick="deleteCustomer(${user.user_id})"><i class="fas fa-trash"></i> Xóa</button>
                    </td>
                </tr>
            `).join('');

            } catch (err) {
                console.error("Lỗi dữ liệu API:", text);
            }
        } catch (error) {
            console.error("Lỗi kết nối:", error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadCustomers(); // Hàm mới thêm
    });

    async function editCustomer(id) {
        try {
            const response = await fetch(`../api/khachhang.php?id=${id}`);
            if (!response.ok) throw new Error("Lỗi kết nối mạng");

            const user = await response.json();

            if (!user || user.error) {
                showToast('Không tìm thấy thông tin khách hàng này!', false);
                return;
            }

            document.getElementById('editUserId').value = user.user_id || '';
            document.getElementById('editHoTen').value = user.ho_ten || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editSdt').value = user.sdt || '';

            const modal = document.getElementById('editModal');
            modal.style.display = 'flex';

        } catch (error) {
            console.error("Lỗi khi gọi API lấy chi tiết:", error);
            showToast('Có lỗi xảy ra: ' + error.message, false);
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    document.getElementById('editForm').onsubmit = async (e) => {
        e.preventDefault();

        // Thu thập dữ liệu từ các ô input
        const updateData = {
            user_id: document.getElementById('editUserId').value,
            ho_ten: document.getElementById('editHoTen').value,
            email: document.getElementById('editEmail').value,
            sdt: document.getElementById('editSdt').value
        };

        try {
            const response = await fetch('../api/update_kh.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updateData)
            });

            const result = await response.json();

            if (result.success) {
                closeEditModal();
                showToast('Cập nhật thành công');
                loadCustomers();
            } else {
                showToast('Lỗi cập nhật: ' + result.error, false);
            }
        } catch (error) {
            console.error("Lỗi khi gửi API cập nhật:", error);
            showToast('Lỗi kết nối khi lưu dữ liệu!', false);
        }
    };

    //them kh
    function openAddModal() {
        document.getElementById('addForm').reset();
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }
    document.getElementById('addForm').onsubmit = async (e) => {
        e.preventDefault();

        const addData = {
            ho_ten: document.getElementById('addHoTen').value.trim(),
            email: document.getElementById('addEmail').value.trim(),
            sdt: document.getElementById('addSdt').value.trim(),
            mat_khau: document.getElementById('addMatKhau').value
        };

        try {
            const response = await fetch('../api/dang_ky_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addData)
            });

            const result = await response.json();

            if (result.success) {
                closeAddModal();
                showToast('Thêm khách hàng thành công');
                loadCustomers();
            } else {
                showToast('Lỗi: ' + result.error, false);
            }
        } catch (error) {
            showToast('Lỗi kết nối máy chủ!', false);
        }
    };

    async function deleteCustomer(id) {
        // Hiện hộp thoại xác nhận
        if (!confirm("Bạn có muốn xóa khách hàng này không?")) return;

        try {
            const response = await fetch('../api/delete_kh.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: id
                })
            });

            // Đọc dữ liệu thô từ server trả về
            const rawText = await response.text();

            // Kiểm tra xem dữ liệu có phải là JSON hợp lệ không
            try {
                const result = JSON.parse(rawText);
                if (result.success) {
                    showToast('Xóa khách hàng thành công');
                    loadCustomers();
                } else {
                    showToast('Lỗi: ' + result.error, false);
                }
            } catch (jsonError) {
                console.error("Server trả về nội dung không phải JSON:", rawText);
                showToast('Lỗi hệ thống!', false);
            }

        } catch (error) {
            console.error("Lỗi kết nối:", error);
            showToast('Không thể kết nối đến máy chủ!', false);
        }
    }


    // Hàm đóng Modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    </script>

</body>

</html>