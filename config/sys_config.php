<?php
// Bật session cho toàn hệ thống - Bắt buộc phải có để làm chức năng Đăng nhập/Đăng xuất
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//  Thêm đúng tên thư mục mẹ "Doancuoiky" vào đường dẫn gốc
define('BASE_URL', 'http://localhost/web_ban_do_an/');
?>