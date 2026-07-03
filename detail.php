<?php
// 1. Cấu hình hệ thống và kết nối Cơ sở dữ liệu
require_once 'config/sys_config.php';
require_once 'config/database.php';

// 2. Lấy ID món ăn từ thanh địa chỉ URL (nếu không có thì quay về trang chủ)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// 3. Truy vấn lấy thông tin chi tiết của món ăn đó dựa vào ID
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Nếu ID không tồn tại trong Database, thông báo lỗi hoặc chuyển trang
    if (!$product) {
        die("<div class='container my-5 text-center'><h3 class='text-danger'>Món ăn này không tồn tại hoặc đã bị xóa!</h3><a href='index.php' class='btn btn-warning mt-3'>Quay về trang chủ</a></div>");
    }
} catch (PDOException $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Chi Tiết Món Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR DÙNG CHUNG -->
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
            </div>
        </div>
    </nav>

    <!-- CHI TIẾT SẢN PHẨM -->
    <div class="container my-5">
        <div class="card shadow-sm border-0 p-4 bg-white">
            <div class="row g-5">
                <!-- Cột hiển thị hình ảnh -->
                <div class="col-12 col-md-6">
                    <?php if (!empty($product['image']) && file_exists('uploads/' . $product['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow-sm w-100" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 400px; object-fit: cover;">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&h=450&fit=crop" class="img-fluid rounded shadow-sm w-100" alt="Ảnh mặc định" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>
                </div>

                <!-- Cột hiển thị thông tin chi tiết -->
                <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
                    <span class="badge bg-danger p-2 px-3 align-self-start mb-3 fs-6 rounded-pill">MÓN NGON NÊN THỬ</span>
                    <h1 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                    <h3 class="text-danger fw-bold mb-4"><?= number_format($product['price'], 0, ',', '.') ?> đ</h3>
                    
                    <h5 class="fw-bold text-secondary">Mô tả món ăn:</h5>
                    <p class="text-muted fs-5 lh-base mb-4">
                        <?= !empty($product['description']) ? htmlspecialchars($product['description']) : 'Món ăn thơm ngon đậm đà hương vị, được chế biến sạch sẽ từ những nguyên liệu tươi ngon nhất, đảm bảo vệ sinh an toàn thực phẩm.' ?>
                    </p>

                    <div class="d-grid gap-2 d-md-block">
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-warning text-white btn-lg fw-bold px-5 py-3 shadow-sm me-md-3">Thêm Vào Giỏ Hàng 🛒</a>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 py-3">Quay Lại</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER DÙNG CHUNG -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>