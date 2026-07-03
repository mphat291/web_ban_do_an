<?php
// 1. Nhúng config hệ thống VÀ file kết nối database vào
require_once 'config/sys_config.php';
require_once 'config/database.php'; // Ní nhớ thêm file kết nối này để xài biến $conn nhé

// Kiểm tra xem khách hàng có bấm chọn món nào không (dựa vào id truyền lên URL)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Nếu giỏ hàng (lưu bằng Session) chưa tồn tại, thì tạo một giỏ hàng trống
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Nếu món ăn này ĐÃ CÓ trong giỏ, thì chỉ cần cộng thêm 1 vào số lượng (không cần truy vấn lại CSDL)
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
        $_SESSION['success_cart'] = "Đã tăng số lượng món ăn trong giỏ hàng!";
    } else {
        // Nếu món này CHƯA CÓ trong giỏ, ta truy vấn CSDL để lấy Tên, Giá, Ảnh của món đó
        try {
            $stmt = $conn->prepare("SELECT name, price, image FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Nếu tìm thấy món ăn hợp lệ trong CSDL thì mới thêm vào giỏ
            if ($product) {
                $_SESSION['cart'][$id] = [
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => 1
                ];
                $_SESSION['success_cart'] = "Đã thêm món ăn vào giỏ hàng thành công!";
            } else {
                $_SESSION['error_cart'] = "Món ăn không tồn tại!";
            }
        } catch (PDOException $e) {
            $_SESSION['error_cart'] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Xử lý xong thì tự động quay ngược trở lại trang chủ theo đúng ý ní
// Bấm phát nhảy vào giỏ hàng xem liền cho nóng
header('Location: cart.php');
exit();
?>