<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

// Đọc từ khóa tìm kiếm gửi từ thanh URL lên
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        // Nếu có từ khóa, truy vấn lọc món ăn chứa từ khóa đó
        $sql = "SELECT * FROM products WHERE name LIKE :search ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
    } else {
        // Nếu không tìm kiếm, lấy hết món ăn bình thường
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $conn->query($sql);
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thực Đơn Món Ngon - CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NHÚNG HEADER / NAVBAR DÙNG CHUNG -->
    <?php include 'header.php'; ?>

    <div class="container my-5">
        
        <!-- KHU VỰC TIÊU ĐỀ & Ô TÌM KIẾM MÓN ĂN -->
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark m-0">🍕 THỰC ĐƠN MÓN NGON</h2>
            </div>
            <div class="col-md-6 text-end">
                <!-- Form Tìm kiếm món ăn gửi dữ liệu qua phương thức GET -->
                <form action="menu.php" method="GET" class="d-flex justify-content-md-end mt-3 mt-md-0">
                    <input class="form-control me-2 style="max-width: 300px;" type="search" name="search" placeholder="Nhập tên món ăn cần tìm..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-warning text-white fw-bold" type="submit">Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="menu.php" class="btn btn-secondary ms-2">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- THÔNG BÁO KHI TÌM KIẾM -->
        <?php if (!empty($search)): ?>
            <p class="text-muted fs-5 mb-4">Kết quả tìm kiếm cho từ khóa: <strong class="text-danger">"<?= htmlspecialchars($search) ?>"</strong></p>
        <?php endif; ?>

        <!-- HIỂN THỊ DANH SÁCH MÓN ĂN DẠNG CARD -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <!-- Ảnh món ăn -->
                            <?php if (!empty($p['image']) && file_exists('uploads/' . $p['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=300&h=200&fit=crop" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <!-- Thân Card thông tin -->
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></h5>
                                    <p class="text-danger fw-bold fs-5"><?= number_format($p['price'], 0, ',', '.') ?> đ</p>
                                    <p class="card-text text-muted small text-truncate"><?= htmlspecialchars($p['description']) ?></p>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Xem chi tiết</a>
                                    <a href="add_to_cart.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning text-white fw-bold">Thêm vào giỏ 🛒</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Trạng thái không tìm thấy món -->
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-4">Không tìm thấy món ăn nào khớp với yêu cầu của ní hết trơn hà! 😢</p>
                    <a href="menu.php" class="btn btn-warning text-white fw-bold">Xem tất cả món ăn</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- NHÚNG FOOTER DÙNG CHUNG -->
    <?php include 'footer.php'; ?>

</body>
</html>