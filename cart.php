<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Kiểm tra xem có thông báo từ phiên trước không
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); 
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update') {
        $qty = intval($_POST['quantity']);
        if ($qty > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$qty, $user_id, $product_id]);
            $_SESSION['message'] = "Đã cập nhật số lượng thành công!";
        }
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $_SESSION['message'] = "Đã xóa món ăn khỏi giỏ!";
    }
    header('Location: cart.php'); 
    exit;
}

// Lấy dữ liệu giỏ hàng
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
    <title>Giỏ Hàng Của Bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php include 'header.php'; ?>

    <div class="container my-5 flex-grow-1">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="fw-bold text-dark mb-4">🛒 GIỎ HÀNG CỦA BẠN</h2>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm p-4 bg-white page-card-rounded">
                    <?php if (!empty($cart)): ?>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Món ăn</th>
                                        <th class="text-center">Giá</th>
                                        <th class="text-center cart-table-qty">Số lượng</th>
                                        <th class="text-center">Tổng cộng</th>
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
                                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="rounded me-3 cart-item-image">
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-danger fw-bold text-center"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <form action="cart.php" method="POST" class="d-flex align-items-center justify-content-center">
                                                <input type="hidden" name="id" value="<?= $item['product_id'] ?>">
                                                <input type="hidden" name="action" value="update">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-danger fw-bold text-center"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
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
                        <div class="mt-4 d-flex justify-content-center">
                            <a href="menu.php" class="btn btn-outline-secondary fw-bold">
                                <span class="me-1">←</span> Tiếp tục chọn món
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <p class="text-muted fs-5">Giỏ hàng của ní đang trống trơn hà! 😢</p>
                            <a href="menu.php" class="btn btn-warning text-white fw-bold px-4">Đi chợ ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white page-card-rounded">
                    <h4 class="fw-bold mb-3 text-dark">Tóm tắt đơn hàng</h4>
                    <?php if (!empty($cart)): ?>
                        <ul class="list-unstyled mb-3">
                            <?php foreach ($cart as $item): ?>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-truncate" style="max-width: 180px;">
                                        <?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?>
                                    </span>
                                    <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex justify-content-between my-3 fs-5 pt-2 border-top">
                            <span class="text-muted">Tổng tiền:</span>
                            <span class="text-danger fw-bold fs-4"><?= number_format($total_money, 0, ',', '.') ?> đ</span>
                        </div>
                        <a href="checkout.php" class="btn btn-warning text-white btn-lg fw-bold w-100 py-2 shadow-sm">Thanh Toán 💳</a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" disabled>Giỏ trống</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>