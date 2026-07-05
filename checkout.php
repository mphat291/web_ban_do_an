<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$cart = $_SESSION['cart'];
$total_money = 0;
foreach ($cart as $item) {
    $total_money += $item['price'] * $item['quantity'];
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $note     = trim($_POST['note'] ?? '');

    if (empty($fullname) || empty($phone) || empty($address)) {
        $error = 'Vui lòng điền đầy đủ Tên, Số điện thoại và Địa chỉ giao hàng!';
    } else {
        try {
            $conn->beginTransaction();
            $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

            $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, note, total_money, status) VALUES (:user_id, :fullname, :phone, :address, :note, :total_money, 'pending')";
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->execute(['user_id' => $user_id, 'fullname' => $fullname, 'phone' => $phone, 'address' => $address, 'note' => $note, 'total_money' => $total_money]);

            $order_id = $conn->lastInsertId();
            $sql_detail = "INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (:order_id, :product_id, :price, :quantity)";
            $stmt_detail = $conn->prepare($sql_detail);
            foreach ($cart as $product_id => $item) {
                $stmt_detail->execute(['order_id' => (int)$order_id, 'product_id' => (int)$product_id, 'price' => (int)$item['price'], 'quantity' => (int)$item['quantity']]);
            }
            $conn->commit();
            unset($_SESSION['cart']);
            $success = true;
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán - CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="checkout-page">
    <div class="container py-5">
        <h2 class="text-center fw-bold mb-5">🛒 Thông Tin Thanh Toán</h2>
        
        <?php if ($success): ?>
            <div class="card shadow p-5 text-center checkout-card">
                <h1 class="text-success mb-3">🎉 Đặt Hàng Thành Công!</h1>
                <p>Cảm ơn bạn đã ủng hộ CaMau Food.</p>
                <a href="index.php" class="btn checkout-btn-warning mt-3">Quay Lại Trang Chủ</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger shadow-sm"><?= $error ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Form nhập -->
                <div class="col-md-7">
                    <div class="card shadow p-4 checkout-card">
                        <h4 class="mb-3 text-secondary">Thông tin nhận hàng</h4>
                        <form action="checkout.php" method="POST">
                            <div class="mb-3"><label class="form-label">Họ và tên</label><input type="text" name="fullname" class="form-control" required></div>
                           <div class="mb-3">
                             <label class="form-label">Số điện thoại</label>
                             <input type="tel" name="phone" class="form-control" 
                                required 
                                pattern="[0-9]{10,11}" 
                                title="Số điện thoại phải từ 10 đến 11 chữ số"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                placeholder="Ví dụ: 0912345678">
                            </div>
                            <div class="mb-3"><label class="form-label">Địa chỉ</label><input type="text" name="address" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Ghi chú</label><textarea name="note" class="form-control"></textarea></div>
                            <button type="submit" class="btn checkout-btn-warning w-100 py-2 mt-2">XÁC NHẬN ĐẶT HÀNG 🚀</button>
                        </form>
                    </div>
                </div>
                <!-- Tóm tắt đơn -->
                <div class="col-md-5">
                    <div class="card shadow p-4 bg-dark text-white checkout-card">
                        <h4 class="mb-3">Đơn hàng của bạn</h4>
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($cart as $item): ?>
                                <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                    <span><?= $item['name'] ?> (x<?= $item['quantity'] ?>)</span>
                                    <span><?= number_format($item['price'] * $item['quantity']) ?>đ</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between fs-4 fw-bold">
                            <span>Tổng cộng:</span>
                            <span class="text-warning"><?= number_format($total_money) ?>đ</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>