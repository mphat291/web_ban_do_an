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

    <?php if (isset($_SESSION['success_cart'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                🎉 <b>Thành công!</b> <?= $_SESSION['success_cart']; unset($_SESSION['success_cart']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'header.php'; ?>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-warning mb-3">Chào Mừng Đến Với CaMau Food</h1>
            <p class="lead fs-4 mb-4">Khám phá thế giới ẩm thực phong phú</p>
            <a href="menu.php" class="btn btn-warning text-white btn-lg fw-bold px-4 py-2 shadow">Xem Thực Đơn Ngay</a>
        </div>
    </header>

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
                            
                            <?php if (!empty($pro['image']) && file_exists('uploads/' . $pro['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($pro['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($pro['name']) ?>" style="height: 240px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=500&h=350&fit=crop" class="card-img-top" alt="Ảnh mặc định" style="height: 240px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($pro['name']) ?></h5>
                                
                                <p class="card-text text-muted flex-grow-1">
                                    <?= !empty($pro['description']) ? htmlspecialchars($pro['description']) : 'Món ăn thơm ngon đậm đà hương vị, được chế biến sạch sẽ từ nguyên liệu tươi ngon.' ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-danger fw-bold fs-5"><?= number_format($pro['price'], 0, ',', '.') ?> đ</span>
                                    <a href="detail.php?id=<?= $pro['id'] ?>" class="btn btn-warning text-white btn-sm fw-bold px-3">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">Hệ thống đang cập nhật món ăn bán chạy, ní vui lòng xem mục thực đơn nhé! 😉</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

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

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>