<?php
require_once 'check_admin.php';
require_once '../config/sys_config.php';
require_once '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// =================== XÓA ĐƠN HÀNG ===================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $order_id = (int)$_GET['delete'];

    try {

        $conn->beginTransaction();

        // Xóa chi tiết đơn hàng trước
        $stmt = $conn->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Xóa đơn hàng
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);

        $conn->commit();

        $_SESSION['success_msg'] = "Đã xóa đơn hàng thành công!";

    } catch (PDOException $e) {

        $conn->rollBack();

        $_SESSION['error_msg'] = "Lỗi: " . $e->getMessage();
    }

    header("Location: orders.php");
    exit();
}

// =================== LẤY DANH SÁCH ĐƠN ===================
try {

    $sql = "
        SELECT
            o.*,
            (
                SELECT GROUP_CONCAT(
                    CONCAT(p.name,' (x',od.quantity,')')
                    SEPARATOR '<br>'
                )
                FROM order_details od
                JOIN products p
                    ON od.product_id = p.id
                WHERE od.order_id = o.id
            ) AS order_items
        FROM orders o
        ORDER BY o.order_date DESC
    ";

    $stmt = $conn->query($sql);

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn Hàng</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow border-0 p-4">

<h1 class="text-center text-danger fw-bold mb-4">
🛵 QUẢN LÝ ĐƠN HÀNG
</h1>

<a href="index.php" class="btn btn-secondary mb-3 fw-bold">
⬅ Quay lại Menu món ăn
</a>

<?php if(isset($_SESSION['success_msg'])): ?>

<div class="alert alert-success">

<?= $_SESSION['success_msg']; ?>

</div>

<?php unset($_SESSION['success_msg']); ?>

<?php endif; ?>

<?php if(isset($_SESSION['error_msg'])): ?>

<div class="alert alert-danger">

<?= $_SESSION['error_msg']; ?>

</div>

<?php unset($_SESSION['error_msg']); ?>

<?php endif; ?>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-success text-center">

<tr>

<th>Mã Đơn</th>

<th>Khách Hàng</th>

<th>Liên Hệ</th>

<th>Địa Chỉ</th>

<th>Chi Tiết Món</th>

<th>Tổng Tiền</th>

<th>Thời Gian</th>

<th>Hành Động</th>

</tr>

</thead>

<tbody>

<?php if(count($orders)>0): ?>

<?php foreach($orders as $ord): ?>

<tr>

<td class="text-center">

#<?= $ord['id']; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($ord['customer_name']); ?>

</strong>

</td>

<td>

<?= htmlspecialchars($ord['phone']); ?>

</td>

<td>

<?= htmlspecialchars($ord['address']); ?>

</td>

<td>

<?= $ord['order_items']; ?>

</td>

<td class="text-danger fw-bold">

<?= number_format($ord['total_price']); ?> đ

</td>

<td>

<?= date('d/m/Y H:i',strtotime($ord['order_date'])); ?>

</td>

<td class="text-center">

<a
href="orders.php?delete=<?= $ord['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">

🗑 Xóa

</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center text-muted py-4">

Hiện chưa có đơn hàng nào!

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>