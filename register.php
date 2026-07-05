<?php
// Nhúng cấu hình và kết nối database giống các trang khác của bạn
require_once 'config/sys_config.php';
require_once 'config/database.php';

$error = '';
$success = '';

// Xử lý khi người dùng nhấn nút Đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra dữ liệu đầu vào cơ bản
    if (empty($username) || empty($fullname) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ tất cả thông tin!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu nhập lại không trùng khớp!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải chứa ít nhất 6 ký tự!';
    } else {
        try {
            // 1. Kiểm tra tài khoản (username) đã tồn tại trong database chưa
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Tài khoản này đã tồn tại trên hệ thống!';
            } else {
                // 2. Mã hóa mật khẩu bằng MD5 để đồng bộ với hệ thống đăng nhập hiện tại
                $hashed_password = md5($password);
                
                // 3. Tiến hành chèn dữ liệu người dùng mới vào bảng dữ liệu (mặc định quyền là 'user')
                $insert_stmt = $conn->prepare("INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, 'user')");
                
                if ($insert_stmt->execute([$username, $hashed_password, $fullname])) {
                    $success = 'Đăng ký tài khoản thành công! Đang chuyển hướng đến trang đăng nhập...';
                    // 🔥 ĐÃ SỬA: Thêm ./ để trình duyệt giữ nguyên trong thư mục Doancuoiky/web_ban_do/
                    header("refresh:2;url=./login.php");
                } else {
                    $error = 'Có lỗi xảy ra trong quá trình lưu dữ liệu!';
                }
            }
        } catch (PDOException $e) {
            $error = 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - CaMau Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center auth-page">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-warning">🍔 CAMAU FOOD</h3>
                            <p class="text-muted">Tạo tài khoản mới của riêng bạn</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger py-2 fs-6 shadow-sm border-0"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success py-2 fs-6 shadow-sm border-0"><?= $success ?></div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên đăng nhập (Tài khoản)</label>
                                <input type="text" name="username" class="form-control" placeholder="Ví dụ: khanhduy123" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Họ và Tên của ní</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Trần Nguyễn Khánh Duy" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mật khẩu</label>
                                <input type="password" name="password" class="form-control" placeholder="Nhập ít nhất 6 ký tự" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu phía trên" required>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 text-white fw-bold shadow-sm mb-3">ĐĂNG KÝ NGAY</button>
                            
                            <div class="text-center">
                                <span class="text-muted small">Đã có tài khoản?</span>
                                <a href="login.php" class="text-warning small fw-bold text-decoration-none">Đăng nhập tại đây</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="index.php" class="text-secondary small text-decoration-none">← Quay lại trang chủ</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>