<?php
session_start();
// Hủy tất cả dữ liệu phiên làm việc
session_unset();
// Hủy phiên làm việc
session_destroy();
// Chuyển hướng về trang đăng nhập
header('Location: ../index.php');
exit;
?>