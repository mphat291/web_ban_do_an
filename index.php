<?php
require_once 'config/sys_config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Hệ Thống Bán Đồ Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <h1 class="text-warning fw-bold">🍔 HỆ THỐNG BÁN ĐỒ ĂN 🍕</h1>
        <p class="lead">Đây là giao diện trang chủ hiển thị món ăn dành cho Khách hàng.</p>
        <hr>

        <?php if (isset($_SESSION['user'])): ?>
            <div class="alert alert-info d-inline-block">
                Xin chào, <b><?= htmlspecialchars($_SESSION['user']['fullname']) ?></b>! 
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn btn-danger btn-sm ms-2">Vào trang Quản trị 👑</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-secondary btn-sm ms-2">Đăng xuất</a>
            </div>
        <?php else: ?>
            <p>Ní muốn đặt hàng hoặc quản trị hệ thống?</p>
            <a href="login.php" class="btn btn-warning text-white fw-bold">Đăng Nhập Ngay</a>
        <?php endif; ?>
        
        <div class="mt-4 p-5 bg-white rounded shadow-sm text-muted">
            <i>[Phần hiển thị danh sách món ăn, danh mục và giỏ hàng sẽ do Thành viên B và C đảm nhiệm triển khai tiếp tại đây]</i>
        </div>
    </div>
</body>
</html>