<?php
require_once '../config/sys_config.php';
require_once '../config/database.php'; // Đảm bảo đường dẫn kết nối CSDL chuẩn xác

$error = '';
$success = '';

// 1. Lấy danh sách danh mục từ database để đổ vào ô Chọn (Select Box)
try {
    $stmt_cate = $conn->query("SELECT id, name FROM categories");
    $categories = $stmt_cate->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error = "Không thể lấy danh sách danh mục: " . $e->getMessage();
}

// 2. Xử lý dữ liệu khi bấm nút "Lưu Món Ăn"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = intval($_POST['price']);
    $category_id = intval($_POST['category_id']); 
    $description = trim($_POST['description']);
    
    // Kiểm tra dữ liệu đầu vào cơ bản
    if (empty($name) || $price <= 0 || $category_id <= 0) {
        $error = 'Vui lòng điền đầy đủ tên món, giá bán và chọn danh mục hợp lệ nha ní!';
    } else {
        // Xử lý upload hình ảnh
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($ext, $allowed_extensions)) {
                $image_name = time() . '_' . uniqid() . '.' . $ext;
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0777, true);
                }
                move_uploaded_file($file_tmp, '../uploads/' . $image_name);
            } else {
                $error = 'Định dạng file ảnh không hợp lệ (Chỉ chấp nhận JPG, PNG, JPEG, WEBP, GIF)!';
            }
        }

        // Nếu không có lỗi gì phát sinh thì tiến hành chèn món ăn mới vào database
        if (empty($error)) {
            try {
                $sql = "INSERT INTO products (name, price, category_id, image, description, created_at) 
                        VALUES (:name, :price, :category_id, :image, :description, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'price' => $price,
                    'category_id' => $category_id, 
                    'image' => $image_name,
                    'description' => $description
                ]);

                // CHỖ THAY ĐỔI ĐỂ TRẢ VỀ TRANG QUẢN TRỊ NÈ NÍ:
                // Tớ cho hiển thị thông báo thành công rồi tự động nhảy về trang products.php sau 1.5 giây cho chuyên nghiệp nhé
                $success = "Thêm thành công! Hệ thống đang chuyển hướng về trang quản trị...";
                header("Refresh: 1.5; url=index.php");
                
                // Mẹo: Nếu muốn bấm phát nhảy về luôn không đợi, ní bỏ comment dòng dưới và xóa 2 dòng trên đi:
                // header('Location: products.php'); exit();

            } catch (PDOException $e) {
                $error = "Lỗi hệ thống không thể thêm món: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Món Ăn Mới - Ban Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5 admin-form-container">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h3 class="fw-bold text-success mb-4 text-center">➕ THÊM MÓN ĂN MỚI</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger shadow-sm"><?= $error ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success shadow-sm"><?= $success ?></div>
            <?php endif; ?>

            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên món</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Matchalatte" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Danh mục món ăn</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục phù hợp --</option>
                        <?php foreach ($categories as $cate): ?>
                            <option value="<?= $cate['id'] ?>"><?= htmlspecialchars($cate['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Giá bán (đ)</label>
                    <input type="number" name="price" class="form-control" placeholder="Ví dụ: 25000" min="1000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh món ăn</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <small class="text-muted">* Hãy chọn file ảnh đẹp mắt (.jpg, .png, .webp) để kích thích vị giác thực khách.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả hương vị, topping kèm theo của món ăn..."></textarea>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success fw-bold py-2 shadow-sm">Lưu Món Ăn 💾</button>
                    <a href="products.php" class="btn btn-secondary fw-bold py-2 shadow-sm">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>