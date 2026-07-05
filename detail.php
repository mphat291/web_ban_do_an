<?php
// 1. Cấu hình hệ thống và kết nối Cơ sở dữ liệu
require_once 'config/sys_config.php';
require_once 'config/database.php';

// Khởi động session nếu hệ thống chưa tự khởi động
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <div class="toast-container position-fixed top-0 end-0 p-3 detail-toast-container">
        <?php if (isset($_SESSION['success_message']) || isset($_GET['success'])): 
            // Lấy câu thông báo từ session hoặc gán mặc định nếu nhận tham số success từ URL
            $msg = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : 'Thành công! Đã tăng số lượng món ăn trong giỏ hàng!';
            // Xóa biến session ngay để không bị lặp lại khi refresh trang
            unset($_SESSION['success_message']);
        ?>
            <div id="cartToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        🎉 <?= htmlspecialchars($msg) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="menu.php">Thực đơn</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Liên hệ</a></li>
                </ul>
                
                <div class="d-flex align-items-center ms-auto"> 
                    
                    <a href="cart.php" class="btn btn-outline-warning btn-sm me-3 position-relative d-flex align-items-center px-2 py-1">
                        <span class="fs-6 me-1">🛒</span> Giỏ hàng
                        
                        <?php 
                        $total_items = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                // Cộng dồn số lượng của từng món ăn
                                $total_items += isset($item['quantity']) ? $item['quantity'] : 1;
                            }
                        }
                        // Nếu có sản phẩm trong giỏ thì mới hiển thị Badge số lượng màu đỏ
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

    <div class="container my-5 flex-grow-1">
        <div class="card shadow-sm border-0 p-4 bg-white page-card-rounded">
            <div class="row g-5">
                <div class="col-12 col-md-6">
                    <?php if (!empty($product['image']) && file_exists('uploads/' . $product['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow-sm w-100 detail-product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&h=450&fit=crop" class="img-fluid rounded shadow-sm w-100 detail-product-image" alt="Ảnh mặc định">
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
                    <span class="badge bg-danger p-2 px-3 align-self-start mb-3 fs-6 rounded-pill">MÓN NGON NÊN THỬ</span>
                    <h1 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                    <h3 class="text-danger fw-bold mb-4"><?= number_format($product['price'], 0, ',', '.') ?> đ</h3>
                    
                    <h5 class="fw-bold text-secondary">Mô tả món ăn:</h5>
                    <p class="text-muted fs-5 lh-base mb-4">
                        <?= !empty($product['description']) ? htmlspecialchars($product['description']) : 'Món ăn thơm ngon đậm đà hương vị, được chế biến sạch sẽ từ những nguyên liệu tươi ngon nhất, đảm bảo vệ sinh an toàn thực phẩm.' ?>
                    </p>

                    <div class="d-grid gap-2 d-md-block">
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn btn-warning text-white btn-lg fw-bold px-5 py-3 shadow-sm me-md-3 detail-action-btn">Thêm Vào Giỏ Hàng 🛒</a>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 py-3 detail-action-btn">Quay Lại</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.getElementById('cartToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
    </script>
</body>
</html>