<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

// 1. Phải đăng nhập mới được thêm vào giỏ
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = "Ní ơi, đăng nhập vào mới đặt món được nha!";
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $user_id = $_SESSION['user']['id'];

    try {
        // 2. Kiểm tra món này đã có trong database cart của user này chưa
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            // Nếu có rồi thì tăng số lượng
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $_SESSION['message'] = "Đã tăng số lượng món ăn trong giỏ hàng!";
        } else {
            // Nếu chưa có thì thêm mới vào bảng cart
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $product_id]);
            $_SESSION['message'] = "Đã thêm món ăn vào giỏ hàng thành công!";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Lỗi hệ thống: " . $e->getMessage();
    }
}

// Chuyển hướng sang giỏ hàng để hiển thị thông báo
header('Location: cart.php');
exit();
?>