Hướng dẫn chạy project bằng XAMPP (Windows)

Bước 1 — Cài XAMPP
- Tải XAMPP từ: https://www.apachefriends.org/index.html
- Cài đặt và mở `XAMPP Control Panel`.
- Bật `Apache` và `MySQL` (Start).

Bước 2 — Đưa project vào thư mục web
- Sao chép toàn bộ thư mục `WebDatSanPic` (đang làm việc) vào `C:\xampp\htdocs\WebDatSanPic`.
  Ví dụ PowerShell:

```powershell
# từ thư mục chứa project
Copy-Item -Recurse -Path . -Destination C:\xampp\htdocs\WebDatSanPic
```

- Hoặc đặt dưới một thư mục con khác và dùng VirtualHost nếu muốn.

Bước 3 — Import database
- Mở phpMyAdmin: http://localhost/phpmyadmin
- Tạo database mới tên `datlichthethao` (collation utf8mb4_general_ci) rồi chọn Import -> upload `database/datlichthethao.sql`.

Hoặc dùng MySQL CLI (trong XAMPP):

```powershell
# tạo database
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS datlichthethao CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
# import
C:\xampp\mysql\bin\mysql.exe -u root datlichthethao < "C:\path\to\WebDatSanPic\database\datlichthethao.sql"
```

Bước 4 — Cấu hình kết nối DB
- Mở file `config/db.example.php` (mình đã tạo mẫu) và chỉnh nếu cần.
- Copy nó thành `config/db.php`:

```powershell
Copy-Item config\db.example.php config\db.php
```

- Nếu bạn đã dùng user/password khác, chỉnh `DB_USER` và `DB_PASS` trong `config/db.php`.

Bước 5 — Mở app
- Mở trình duyệt: http://localhost/WebDatSanPic/index.php
- Nếu bạn đặt project trực tiếp trong `htdocs` thì đường dẫn là như trên.

Ghi chú và lỗi thường gặp
- Nếu Apache bị chiếm port 80, đổi port trong XAMPP Control Panel -> Config -> httpd.conf (ví dụ sang 8080) và truy cập `http://localhost:8080/...`.
- Nếu project dùng extensions PHP không có sẵn, bật chúng trong `php.ini` (XAMPP Control Panel -> Config -> php.ini) và restart Apache.

 Nếu muốn, mình có thể:
 - tạo sẵn `config/db.php` với credentials mặc định cho XAMPP (root, no password), hoặc
 - giúp cấu hình VirtualHost tự động.

 Tự động hóa (script)
 ---------------------
 Mình đã thêm script tiện lợi để tự động copy project vào `C:\xampp\htdocs\WebDatSanPic`, khởi XAMPP và import database.

 Chạy script (PowerShell với quyền Administrator):

 ```powershell
 # từ thư mục project
 cd "C:\path\to\WebDatSanPic"
 .\scripts\setup_xampp.ps1
 ```

 Script sẽ:
 - kiểm tra `C:\xampp` tồn tại
 - copy project vào `htdocs` (sao lưu nếu tồn tại)
 - chạy `xampp_start.exe` nếu có
 - tạo database `datlichthethao` và import `database/datlichthethao.sql`
 - mở `http://localhost/WebDatSanPic/index.php`

 Nếu gặp lỗi, mở XAMPP Control Panel và import SQL qua phpMyAdmin.

