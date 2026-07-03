<?php
// 1. Kết nối hệ thống và cơ sở dữ liệu
require_once 'config/sys_config.php';
require_once 'config/database.php';

// Kiểm tra nếu giỏ hàng trống thì không cho thanh toán, đá về trang chủ
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

// 2. Xử lý khi khách hàng bấm nút "Xác nhận đặt hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $note = trim($_POST['note']);

    if (empty($fullname) || empty($phone) || empty($address)) {
        $error = 'Vui lòng điền đầy đủ Tên, Số điện thoại và Địa chỉ giao hàng ní ơi!';
    } else {
        try {
            // Dùng Transaction để đảm bảo lưu thành công cả bảng orders lẫn order_details
            $conn->beginTransaction();

            // Lấy ID người dùng nếu họ đã đăng nhập, ngược lại để null (khách vãng lai)
            $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

            // Bước A: Chèn thông tin vào bảng `orders`
            $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, note, total_money, status, created_at) 
                          VALUES (:user_id, :fullname, :phone, :address, :note, :total_money, 'pending', NOW())";
            
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->execute([
                'user_id' => $user_id,
                'fullname' => $fullname,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'total_money' => $total_money
            ]);

            // Lấy ra mã đơn hàng vừa mới tự động sinh ra
            $order_id = $conn->lastInsertId();

            // Bước B: Chèn danh sách món ăn trong giỏ vào bảng `order_details`
            $sql_detail = "INSERT INTO order_details (order_id, product_id, price, quantity) 
                           VALUES (:order_id, :product_id, :price, :quantity)";
            $stmt_detail = $conn->prepare($sql_detail);

            foreach ($cart as $product_id => $item) {
                $stmt_detail->execute([
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);
            }

            // Nếu mọi thứ chạy mượt mà, tiến hành commit lưu vào database chính thức
            $conn->commit();

            // Xóa sạch giỏ hàng sau khi đặt thành công
            unset($_SESSION['cart']);
            $success = true;

        } catch (PDOException $e) {
            // Nếu có lỗi xảy ra, hủy bỏ toàn bộ tiến trình để tránh rác database
            $conn->rollBack();
            $error = 'Lỗi hệ thống không thể lưu đơn: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Đơn Hàng - CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">🍔 CaMau Food</a>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 900px;">
        <?php if ($success): ?>
            <!-- Thông báo khi đặt hàng thành công -->
            <div class="card border-0 shadow-sm p-5 text-center bg-white">
                <h1 class="display-4 text-success mb-3">🎉 Đặt Hàng Thành Công!</h1>
                <p class="fs-5 text-muted mb-4">Đơn hàng của ní đã được hệ thống ghi nhận. Nhà bếp CaMau Food đang chuẩn bị món ăn và sẽ giao siêu tốc đến ní nhé!</p>
                <a href="index.php" class="btn btn-warning text-white btn-lg fw-bold px-5 py-3">Quay Lại Trang Chủ</a>
            </div>
        <?php else: ?>
            <h2 class="fw-bold mb-4">💳 THÔNG TIN THANH TOÁN</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Cột nhập thông tin giao hàng -->
                <div class="col-12 col-md-7">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h4 class="fw-bold mb-3 text-secondary">Thông nhận hàng</h4>
                        <form action="checkout.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Họ và tên người nhận</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['fullname']) : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                                <input type="tel" name="phone" class="form-control" placeholder="Ví dụ: 0912345678" value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['phone'] ?? '') : '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa chỉ nhận đồ ăn</label>
                                <input type="text" name="address" class="form-control" placeholder="Số nhà, tên đường, phường/xã..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ghi chú cho nhà bếp (nếu có)</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Món này không bỏ hành, giao cay nhiều giúp em..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning text-white btn-lg fw-bold w-100 py-3 mt-2 shadow-sm">XÁC NHẬN ĐẶT HÀNG 🚀</button>
                        </form>
                    </div>
                </div>

                <!-- Cột tóm tắt lại các món đã đặt -->
                <div class="col-12 col-md-5">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h4 class="fw-bold mb-3 text-secondary">Đơn hàng của ní</h4>
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($cart as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <h6 class="my-0 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted">SL: <?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?> đ</small>
                                    </div>
                                    <span class="text-danger fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between px-0 pt-3 fs-5 fw-bold text-dark">
                                <span>Tổng tiền thanh toán:</span>
                                <span class="text-danger"><?= number_format($total_money, 0, ',', '.') ?> đ</span>
                            </li>
                        </ul>
                        <a href="cart.php" class="btn btn-outline-secondary btn-sm w-100">Sửa lại giỏ hàng</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>