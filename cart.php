<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// 2. XỬ LÝ HÀNH ĐỘNG (Update số lượng hoặc Xóa món)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update') {
        $qty = intval($_POST['quantity']);
        if ($qty > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$qty, $user_id, $product_id]);
        }
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    }
    // Tải lại trang để cập nhật giao diện
    header('Location: cart.php');
    exit;
}

// 3. LẤY DỮ LIỆU GIỎ HÀNG TỪ DATABASE
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_money = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng Của Bạn - CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
        </div>
    </nav>

    <!-- GIAO DIỆN CHÍNH -->
    <div class="container my-5">
        <h2 class="fw-bold text-dark mb-4">🛒 GIỎ HÀNG CỦA BẠN</h2>

        <div class="row g-4">
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
                                    <?php foreach ($cart as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total_money += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-danger fw-bold"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <form action="cart.php" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="id" value="<?= $item['product_id'] ?>">
                                                <input type="hidden" name="action" value="update">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-danger fw-bold"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                                        <td class="text-center">
                                            <form action="cart.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $item['product_id'] ?>">
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
                        <div class="text-center py-5">
                            <p class="text-muted fs-5 mb-4">Giỏ hàng của ní đang trống trơn hà! 😢</p>
                            <a href="menu.php" class="btn btn-warning text-white fw-bold px-4">Đi chợ chọn món ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TỔNG KẾT ĐƠN HÀNG -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h4 class="fw-bold mb-4">Tóm tắt đơn hàng</h4>
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span>Tổng tiền:</span>
                        <span class="text-danger fw-bold"><?= number_format($total_money, 0, ',', '.') ?> đ</span>
                    </div>
                    <?php if (!empty($cart)): ?>
                        <a href="checkout.php" class="btn btn-warning text-white btn-lg fw-bold w-100 py-3">Thanh Toán 💳</a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg fw-bold w-100 py-3" disabled>Giỏ trống</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>