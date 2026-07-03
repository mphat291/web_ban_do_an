<?php
// Kiểm tra nếu chưa có session thì khởi động session lên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa, hoặc tài khoản có phải là admin không
// Dựa trên cấu trúc session của ní: $_SESSION['user']['role'] === 'admin'
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Nếu không phải admin, đá văng người dùng ra trang login ở ngoài thư mục gốc
    header("Location: ../login.php");
    exit();
}
?>