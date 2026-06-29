<?php
require_once 'config/sys_config.php';

// Xóa sạch toàn bộ session đăng nhập
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Điều hướng người dùng quay lại trang đăng nhập
header('Location: ' . BASE_URL . 'login.php');
exit();