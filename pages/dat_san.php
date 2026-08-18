<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickleBall UTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/datsan.css">
</head>
<body>
    <a href="../index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="../assets/img/logo.png" style="width: 80px; height: 80px; margin-right: 8px;">
                <span>PickleBall UTT</span>
            </div>
        </header>
    </div>

    <div class="booking-page">
        <div class="booking-left">
            <div class="section-card">
                <h2 class="section-title"><i class="fas fa-calendar-check"></i> Thông Tin Đặt Sân</h2>
                <div class="info-grid" id="bookingInfo">
                    <!-- JS render -->
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title">Dịch Vụ Thêm</h2>
                <div id="servicesList">
                    <div class="loading"><i class="fas fa-spinner fa-spin"></i> Đang tải dịch vụ...</div>
                </div>
            </div>
        </div>

        <div class="booking-right">
            <div class="summary-card">
                <h2 class="section-title"><i class="fas fa-receipt"></i> Tóm Tắt Đơn</h2>
                <div class="summary-details" id="summaryDetails">
                    <!-- JS render -->
                </div>
                <div class="summary-line"></div>
                <div class="summary-row">
                    <span>Tiền sân</span>
                    <strong id="sTienSan">0đ</strong>
                </div>
                <div class="summary-row">
                    <span>Tiền dịch vụ</span>
                    <strong id="sTienDV">0đ</strong>
                </div>
                <div class="summary-line"></div>
                <div class="summary-row summary-total">
                    <span>Tổng cộng</span>
                    <strong id="sTotal">0đ</strong>
                </div>
                <button class="confirm-btn" id="confirmBtn"><i class="fas fa-check-circle"></i> Xác Nhận Đặt Sân</button>
                <button class="back-link" id="cancelBtn"><i class="fas fa-arrow-left"></i> Quay lại chọn sân</button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        function showToast(msg, ok = true) {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed; top:24px; right:24px; z-index:9999; padding:14px 24px; border-radius:12px; font-weight:600; color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.3s; background:${ok ? '#0a3b3b' : '#e76f51'};`;
            t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
            document.body.appendChild(t);
            setTimeout(() => t.style.transform = 'translateX(0)', 10);
            setTimeout(() => { t.style.transform = 'translateX(120%)'; setTimeout(() => t.remove(), 300); }, 3000);
        }

        const user = JSON.parse(localStorage.getItem('user'));
        if (!user) {
            showToast('Vui lòng đăng nhập!', false);
            setTimeout(() => { window.location.href = 'dang_nhap.php'; }, 1200);
            return;
        }
        

        // Lấy thông tin từ url
        const params = new URLSearchParams(window.location.search);
        const bookingData = {
            san_id: params.get('san_id'),
            khung_gio_id: params.get('khung_gio_id'),
            ngay_dat: params.get('ngay_dat'),
            time: params.get('time'),
            price: Number(params.get('price')),
            co_so: params.get('co_so'),
            khu_vuc: params.get('khu_vuc'),
            san: params.get('san')
        };

        if (!bookingData.san_id || !bookingData.khung_gio_id || !bookingData.ngay_dat) {
            showToast('Thông tin đặt sân không hợp lệ!', false);
            setTimeout(() => { window.location.href = '../index.php'; }, 1200);
            return;
        }

        // Render thông tin đặt sân
        document.getElementById('bookingInfo').innerHTML = `
            <div class="info-item"><i class="fas fa-building"></i><div><small>Cơ sở</small><strong>${bookingData.co_so}</strong></div></div>
            <div class="info-item"><i class="fas fa-map-marker-alt"></i><div><small>Khu vực</small><strong>${bookingData.khu_vuc}</strong></div></div>
            <div class="info-item"><i class="fas fa-table-tennis"></i><div><small>Sân</small><strong>${bookingData.san}</strong></div></div>
            <div class="info-item"><i class="far fa-clock"></i><div><small>Khung giờ</small><strong>${bookingData.time}</strong></div></div>
            <div class="info-item"><i class="far fa-calendar"></i><div><small>Ngày</small><strong>${bookingData.ngay_dat}</strong></div></div>
            <div class="info-item"><i class="fas fa-money-bill-wave"></i><div><small>Giá sân</small><strong>${bookingData.price.toLocaleString('vi-VN')}đ</strong></div></div>
        `;

        // Render tóm tắt đơn
        document.getElementById('summaryDetails').innerHTML = `
            <p><strong>${bookingData.san}</strong> — ${bookingData.khu_vuc}</p>
            <p>${bookingData.time} · ${bookingData.ngay_dat}</p>
        `;
        document.getElementById('sTienSan').textContent = bookingData.price.toLocaleString('vi-VN') + 'đ';
        document.getElementById('sTotal').textContent = bookingData.price.toLocaleString('vi-VN') + 'đ';

        // Load dịch vụ
        fetch('../api/get_dich_vu.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.data.length) {
                document.getElementById('servicesList').innerHTML = '<p class="no-service">Không có dịch vụ nào</p>';
                return;
            }
            let html = '';
            data.data.forEach(dv => {
                html += `
                <div class="service-item">
                    <label class="service-check">
                        <input type="checkbox" class="dv-cb" data-id="${dv.dich_vu_id}" data-price="${dv.don_gia}" data-name="${dv.ten_dich_vu}">
                        <div class="service-body">
                            <span class="service-name">${dv.ten_dich_vu}</span>
                            <span class="service-desc">${dv.mo_ta || ''}</span>
                        </div>
                    </label>
                    <div class="service-right">
                        <span class="service-price">${Number(dv.don_gia).toLocaleString('vi-VN')}đ<small>/${dv.don_vi}</small></span>
                        <div class="qty-control">
                            <button class="qty-btn qty-minus" data-id="${dv.dich_vu_id}">−</button>
                            <span class="qty-val" id="qty-${dv.dich_vu_id}">1</span>
                            <button class="qty-btn qty-plus" data-id="${dv.dich_vu_id}">+</button>
                        </div>
                    </div>
                </div>`;
            });
            document.getElementById('servicesList').innerHTML = html;

            function updateTotal() {
                let tienDV = 0;
                document.querySelectorAll('.dv-cb:checked').forEach(cb => {
                    const price = Number(cb.dataset.price);
                    const qty = Number(document.getElementById('qty-' + cb.dataset.id).textContent);
                    tienDV += price * qty;
                });
                document.getElementById('sTienDV').textContent = tienDV.toLocaleString('vi-VN') + 'đ';
                document.getElementById('sTotal').textContent = (bookingData.price + tienDV).toLocaleString('vi-VN') + 'đ';
            }

            document.getElementById('servicesList').addEventListener('change', updateTotal);
            document.getElementById('servicesList').addEventListener('click', function(e) {
                if (e.target.classList.contains('qty-plus')) {
                    const el = document.getElementById('qty-' + e.target.dataset.id);
                    el.textContent = Number(el.textContent) + 1;
                    updateTotal();
                } else if (e.target.classList.contains('qty-minus')) {
                    const el = document.getElementById('qty-' + e.target.dataset.id);
                    if (Number(el.textContent) > 1) el.textContent = Number(el.textContent) - 1;
                    updateTotal();
                }
            });
        });

        // Xác nhận đặt sân
        document.getElementById('confirmBtn').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            const dichVu = [];
            document.querySelectorAll('.dv-cb:checked').forEach(cb => {
                const qty = Number(document.getElementById('qty-' + cb.dataset.id).textContent);
                dichVu.push({
                    dich_vu_id: cb.dataset.id,
                    so_luong: qty,
                    thanh_tien: Number(cb.dataset.price) * qty
                });
            });

            fetch('../api/dat_san_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: user.user_id,
                    san_id: bookingData.san_id,
                    khung_gio_id: bookingData.khung_gio_id,
                    ngay_dat: bookingData.ngay_dat,
                    tien_san: bookingData.price,
                    dich_vu: dichVu
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast('Đặt sân thành công! Đơn đang chờ xác nhận.');
                    setTimeout(() => { window.location.href = 'tai_khoan_khach.php?tab=history'; }, 1200);
                } else {
                    showToast('Lỗi: ' + result.error, false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Có lỗi xảy ra!', false);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Xác Nhận Đặt Sân';
            });
        });

        document.getElementById('cancelBtn').addEventListener('click', () => window.history.back());
    })();
    </script>
</body>
</html>