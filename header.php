<?php
// Đảm bảo đã có kết nối database và khởi động session
require_once 'config/sys_config.php';
require_once 'config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Thực đơn</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Liên hệ</a></li>
                </ul>
                
                <div class="d-flex align-items-center ms-auto"> 
                    <a href="cart.php" class="btn btn-outline-warning btn-sm me-3 position-relative d-flex align-items-center px-2 py-1">
                        <span class="fs-6 me-1">🛒</span> Giỏ hàng
                        <?php 
                        $total_items = 0;
                        if (isset($_SESSION['user'])) {
                            $user_id = $_SESSION['user']['id'];
                            $stmt_count = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                            $stmt_count->execute([$user_id]);
                            $result = $stmt_count->fetch(PDO::FETCH_ASSOC);
                            $total_items = $result['total'] ?? 0;
                        }
                        if ($total_items > 0): 
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                <?= $total_items ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user'])): ?>
                        <span class="text-light me-3">Xin chào, <b class="text-warning"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></b>!</span>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="admin/index.php" class="btn btn-danger btn-sm me-2">Quản trị 👑</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-secondary btn-sm">Đăng xuất</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-warning text-white fw-bold btn-sm me-2">Đăng Nhập</a>
                        <a href="register.php" class="btn btn-outline-light btn-sm fw-bold">Đăng Ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['message']) && basename($_SERVER['PHP_SELF']) == 'cart.php'): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>