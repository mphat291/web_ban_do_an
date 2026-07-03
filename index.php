<?php
// 1. Cấu hình hệ thống và kết nối Cơ sở dữ liệu
require_once 'config/sys_config.php';
require_once 'config/database.php';

// 2. Lấy danh sách 3 món ăn mới nhất/bán chạy từ database lên trang chủ
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 3");
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $top_products = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - CaMau Food</title>
    <!-- Nhúng Bootstrap 5 để làm giao diện hiện đại, responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1200&h=400&fit=crop');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
    </style>
</head>
<body class="bg-light">

 <!-- ĐOẠN HIỂN THỊ THÔNG BÁO GIỎ HÀNG (CHÈN VÀO ĐÂY) -->
    <?php if (isset($_SESSION['success_cart'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                🎉 <b>Thành công!</b> <?= $_SESSION['success_cart']; unset($_SESSION['success_cart']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- (Tiếp tục là phần Navbar và Hero Banner bên dưới của ní...) -->

    <!-- NAVBAR ĐIỀU HƯỚNG DÙNG CHUNG -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Thực đơn</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Liên hệ</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <!-- Form Tìm kiếm món ăn -->
                    <form class="d-flex me-3" action="search.php" method="GET">
                        <input class="form-control form-control-sm me-2" type="search" name="keyword" placeholder="Tìm món ăn..." aria-label="Search">
                        <button class="btn btn-outline-warning btn-sm" type="submit">Tìm</button>
                    </form>
                    
                    <!-- Xử lý trạng thái Đăng nhập / Quản trị viên -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <span class="text-light me-3">Xin chào, <b class="text-warning"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></b>!</span>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="admin/index.php" class="btn btn-danger btn-sm me-2">Quản trị 👑</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-secondary btn-sm">Đăng xuất</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-warning text-white fw-bold btn-sm">Đăng Nhập</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO BANNER GIỚI THIỆU -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-warning mb-3">Chào Mừng Đến Với CaMau Food</h1>
            <p class="lead fs-4 mb-4">Khám phá thế giới ẩm thực phong phú, chuẩn vị miền Tây sông nước!</p>
            <a href="menu.php" class="btn btn-warning text-white btn-lg fw-bold px-4 py-2 shadow">Xem Thực Đơn Ngay</a>
        </div>
    </header>

    <!-- KHU VỰC SẢN PHẨM BÁN CHẠY NHẤT (ĐỘNG TỪ DATABASE) -->
    <section class="container my-5">
        <div class="text-center mb-5">
            <h2 class="text-warning fw-bold text-uppercase">🔥 MÓN ĂN BÁN CHẠY NHẤT 🔥</h2>
            <p class="text-muted">Những món ăn siêu ngon được cộng đồng CaMau Food săn đón nhiều nhất</p>
            <hr class="w-25 mx-auto text-warning" style="height: 3px; opacity: 1;">
        </div>

        <div class="row g-4">
            <?php if (!empty($top_products)): ?>
                <?php foreach ($top_products as $pro): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 position-relative">
                            <span class="position-absolute top-0 start-0 badge bg-danger m-3 px-3 py-2 fs-6 shadow">HOT</span>
                            
                            <!-- Hiển thị ảnh động chuẩn xác -->
                            <?php if (!empty($pro['image']) && file_exists('uploads/' . $pro['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($pro['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($pro['name']) ?>" style="height: 240px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=500&h=350&fit=crop" class="card-img-top" alt="Ảnh mặc định" style="height: 240px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <!-- Tên món ăn động -->
                                <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($pro['name']) ?></h5>
                                
                                <!-- Mô tả món ăn động -->
                                <p class="card-text text-muted flex-grow-1">
                                    <?= !empty($pro['description']) ? htmlspecialchars($pro['description']) : 'Món ăn thơm ngon đậm đà hương vị, được chế biến sạch sẽ từ nguyên liệu tươi ngon.' ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <!-- Giá tiền format VND -->
                                    <span class="text-danger fw-bold fs-5"><?= number_format($pro['price'], 0, ',', '.') ?> đ</span>
                                    <!-- Nút xem chi tiết kết nối trực tiếp với detail.php kèm ID chuẩn -->
                                    <a href="detail.php?id=<?= $pro['id'] ?>" class="btn btn-warning text-white btn-sm fw-bold px-3">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback nếu database trống -->
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">Hệ thống đang cập nhật món ăn bán chạy, ní vui lòng xem mục thực đơn nhé! 😉</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- THÔNG TIN CAM KẾT -->
    <section class="bg-white py-5 border-top border-bottom">
        <div class="container text-center">
            <div class="row g-4">
                <div class="col-md-4">
                    <h4 class="fw-bold text-dark">🚀 Giao Hàng Siêu Tốc</h4>
                    <p class="text-muted mb-0">Đảm bảo món ăn luôn nóng hổi khi đến tay bạn.</p>
                </div>
                <div class="col-md-4">
                    <h4 class="fw-bold text-dark">🥗 Nguyên Liệu Sạch</h4>
                    <p class="text-muted mb-0">Lựa chọn nghiêm ngặt, chuẩn an toàn vệ sinh thực phẩm.</p>
                </div>
                <div class="col-md-4">
                    <h4 class="fw-bold text-dark">📞 Hỗ Trợ 24/7</h4>
                    <p class="text-muted mb-0">Luôn sẵn sàng lắng nghe ý kiến đóng góp của ní.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER DÙNG CHUNG -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>