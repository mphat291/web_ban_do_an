<?php
require_once '../config/sys_config.php';

// Bảo mật: Nếu chưa đăng nhập hoặc không phải admin thì đá bay ra trang login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Hệ Thống Bán Đồ Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-sm">
            <h1 class="text-danger fw-bold">👑 TRANG QUẢN TRỊ ADMIN 👑</h1>
            <p class="lead">Chào mừng, <b><?= htmlspecialchars($_SESSION['user']['fullname']) ?></b> đã đăng nhập thành công!</p>
            <hr>
            <p>Hệ thống phân quyền Auth đã chạy chuẩn đét. Giờ ní có thể bàn giao folder <code>admin</code> này cho bạn làm chức năng CRUD món ăn rồi đó!</p>
            <a href="../logout.php" class="btn btn-dark mt-3">Đăng xuất hệ thống</a>
        </div>
    </div>
</body>
</html>