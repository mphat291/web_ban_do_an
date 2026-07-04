<?php
// Bật session cho toàn hệ thống - Bắt buộc phải có để làm chức năng Đăng nhập/Đăng xuất
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tự động nhận diện giao thức (http hoặc https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Tự động lấy tên miền và cổng (localhost)
$domainName = $_SERVER['HTTP_HOST'];

// Tự động tìm thư mục chứa file đang chạy, xử lý luôn cả dấu gạch chéo ngược trên Windows
$currentDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Chuẩn hóa đường dẫn để lấy chính xác thư mục gốc của dự án (web_ban_do_an)
$projectDir = preg_replace('/(.*\/web_ban_do_an).*/', '$1', $currentDir);
$baseUrl = $protocol . $domainName . rtrim($projectDir, '/') . '/';

// Định nghĩa hằng số BASE_URL tự động - Hoạt động hoàn hảo trên mọi máy tính trong nhóm
define('BASE_URL', $baseUrl);
?>