<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

try {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $stmt = $conn->query($sql);
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
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php include 'header.php'; ?>

    <div class="container my-5 flex-grow-1 d-flex flex-column">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark m-0">🍕 THỰC ĐƠN MÓN NGON</h2>
            </div>
            <div class="col-md-6 text-end">
                <form id="search-form" action="menu.php" method="GET" class="d-flex justify-content-md-end mt-3 mt-md-0" onsubmit="return false;">
                    <input id="live-search" class="form-control menu-search-input" type="search" placeholder="Nhập tên món ăn cần tìm...">
                    <a href="menu.php" id="reset-search-btn" class="btn btn-secondary ms-2 d-none">Reset</a>
                </form>
            </div>
        </div>

        <div id="search-status-wrapper"></div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="food-list-container">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <div class="col food-card-item">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <?php if (!empty($p['image']) && file_exists('uploads/' . $p['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top menu-food-image">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=300&h=200&fit=crop" class="card-img-top menu-food-image">
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold text-dark food-card-title"><?= htmlspecialchars($p['name']) ?></h5>
                                    <p class="text-danger fw-bold fs-5"><?= number_format($p['price'], 0, ',', '.') ?> đ</p>
                                    <p class="card-text text-muted small text-truncate"><?= htmlspecialchars($p['description']) ?></p>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Xem chi tiết</a>
                                    
                                    <?php if (isset($_SESSION['user'])): ?>
                                        <a href="add_to_cart.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning text-white fw-bold">Thêm vào giỏ 🛒</a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-sm btn-secondary text-white fw-bold">Đăng nhập để mua</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="no-food-alert" class="flex-grow-1 d-none flex-column align-items-center justify-content-center text-center py-5">
            <div class="mb-4 empty-state-emoji">🔍😢</div>
            <h3 class="fw-bold text-dark mb-3 empty-state-title">Không tìm thấy món ăn nào khớp với yêu cầu của ní hết trơn hà!</h3>
            <p class="text-muted mb-4 fs-5">Hãy thử tìm kiếm với một từ khóa khác xem sao nhé.</p>
            <a href="menu.php" class="btn btn-warning btn-lg text-white fw-bold px-4 py-2 shadow-sm empty-state-action">Xem tất cả món ăn</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('live-search');
        const foodItems = document.querySelectorAll('.food-card-item');
        const noFoodAlert = document.getElementById('no-food-alert');
        const searchStatusWrapper = document.getElementById('search-status-wrapper');
        const resetBtn = document.getElementById('reset-search-btn');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                let visibleCount = 0;

                foodItems.forEach(function (item) {
                    const titleElement = item.querySelector('.food-card-title');
                    if (titleElement) {
                        const foodTitle = titleElement.textContent.toLowerCase();
                        if (foodTitle.includes(keyword)) {
                            item.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            item.classList.add('d-none');
                        }
                    }
                });

                if (keyword !== '') {
                    searchStatusWrapper.innerHTML = `<p class="text-muted fs-5 mb-4">Kết quả tìm kiếm cho từ khóa: <strong class="text-danger">"${this.value}"</strong></p>`;
                    resetBtn.classList.remove('d-none');
                } else {
                    searchStatusWrapper.innerHTML = '';
                    resetBtn.classList.add('d-none');
                }

                // Chuyển đổi trạng thái hiển thị linh hoạt (d-flex để căn giữa dọc ngang)
                if (visibleCount === 0) {
                    noFoodAlert.classList.remove('d-none');
                    noFoodAlert.classList.add('d-flex');
                } else {
                    noFoodAlert.classList.add('d-none');
                    noFoodAlert.classList.remove('d-flex');
                }
            });
        }
    });
    </script>
</body>
</html>