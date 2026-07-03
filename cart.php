<?php
require_once 'config/sys_config.php';

// 1. TỰ ĐỘNG LỌC RÁC: Xóa các món bị lỗi thiếu key (nếu có) trong Session cũ
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $item) {
        // Nếu món ăn thiếu tên hoặc thiếu giá, tự động dọn dẹp luôn
        if (!isset($item['name']) || !isset($item['price'])) {
            unset($_SESSION['cart'][$id]);
        }
    }
}

// 2. XỬ LÝ HÀNH ĐỘNG: Cập nhật số lượng hoặc Xóa món ăn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update') {
        $qty = intval($_POST['quantity']);
        if ($qty > 0 && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    } elseif ($_POST['action'] === 'delete') {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }
    // Xử lý xong load lại trang tại chỗ cho sạch dữ liệu
    header('Location: cart.php');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_money = 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng Của Bạn - CaMau Food</title>
    <!-- Nhúng Bootstrap 5 chuẩn chỉnh -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR DÙNG CHUNG -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Thực đơn</a></li>
                </ul>
                <a href="cart.php" class="btn btn-outline-warning position-relative">
                    🛒 Giỏ hàng
                    <?php if (!empty($cart)): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= count($cart) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- GIAO DIỆN CHÍNH CỦA GIỎ HÀNG -->
    <div class="container my-5">
        <h2 class="fw-bold text-dark mb-4">🛒 GIỎ HÀNG CỦA BẠN</h2>

        <div class="row g-4">
            <!-- Bên trái: Danh sách các món ăn trong giỏ -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <?php if (!empty($cart)): ?>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Món ăn</th>
                                        <th>Giá</th>
                                        <th style="width: 130px;">Số lượng</th>
                                        <th>Tổng cộng</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $id => $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total_money += $subtotal;
                                    ?>
                                        <tr>
                                            <!-- Hình ảnh & Tên món -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($item['image']) && file_exists('uploads/' . $item['image'])): ?>
                                                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=60&h=60&fit=crop" class="rounded me-3" alt="Mặc định">
                                                    <?php endif; ?>
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></span>
                                                </div>
                                            </td>
                                            
                                            <!-- Đơn giá -->
                                            <td class="text-danger fw-bold"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                            
                                            <!-- Ô thay đổi số lượng (Thay đổi là tự submit thay đổi số tiền luôn) -->
                                            <td>
                                                <form action="cart.php" method="POST" class="d-flex align-items-center">
                                                    <input type="hidden" name="id" value="<?= $id ?>">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            
                                            <!-- Tổng tiền của món đó -->
                                            <td class="text-danger fw-bold"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                                            
                                            <!-- Nút xóa món -->
                                            <td class="text-center">
                                                <form action="cart.php" method="POST">
                                                    <input type="hidden" name="id" value="<?= $id ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none fw-bold">Xóa 🗑️</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Giao diện hiển thị khi giỏ hàng trống -->
                        <div class="text-center py-5">
                            <p class="text-muted fs-5 mb-4">Giỏ hàng của ní đang trống trơn hà! 😢</p>
                            <a href="menu.php" class="btn btn-warning text-white fw-bold px-4">Đi chợ chọn món ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bên phải: Tóm tắt đơn hàng & nút Bấm thanh toán -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span>Tổng tiền món:</span>
                        <span class="text-danger fw-bold"><?= number_format($total_money, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 text-muted">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success fw-bold">Miễn phí 🚀</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 fs-4 fw-bold text-dark">
                        <span>Thành tiền:</span>
                        <span class="text-danger"><?= number_format($total_money, 0, ',', '.') ?> đ</span>
                    </div>
                    
                    <!-- Kiểm tra nếu có món ăn thì mới cho bấm nút thanh toán sang checkout.php -->
                    <?php if (!empty($cart)): ?>
                        <a href="checkout.php" class="btn btn-warning text-white btn-lg fw-bold w-100 py-3 shadow-sm">Tiến Hành Thanh Toán 💳</a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg fw-bold w-100 py-3" disabled>Chưa có món để thanh toán</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>