<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin/index.php');
    } else {
        header('Location: ' . BASE_URL . 'index.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ tên đăng nhập và mật khẩu ní ơi!';
    } else {
        try {
            // Mã hóa MD5 mật khẩu người dùng gõ vào để so khớp trực tiếp
            $password_md5 = md5($password);

            $sql = "SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'username' => $username,
                'password' => $password_md5
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role']
                ];

                if ($user['role'] === 'admin') {
                    header('Location: ' . BASE_URL . 'admin/index.php');
                } else {
                    header('Location: ' . BASE_URL . 'index.php');
                }
                exit();
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng rồi ní ơi!';
            }
        } catch (PDOException $e) {
            $error = 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Hệ Thống Bán Đồ Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center auth-page">

<div class="card auth-card p-4">
    <div class="card-body">
        <h3 class="text-center mb-4 text-warning fw-bold">🍔 ĐĂNG NHẬP 🍕</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center py-2" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" name="username" required placeholder="Nhập tài khoản...">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required placeholder="Nhập mật khẩu...">
            </div>
            <button type="submit" class="btn btn-warning w-100 text-white fw-bold mt-2">ĐĂNG NHẬP NGAY</button>
        </form>

        <!-- Thêm phần link Đăng ký và Quay lại trang chủ -->
        <div class="text-center mt-4">
            <p class="mb-2 auth-link-small">Chưa có tài khoản? <a href="register.php" class="text-warning text-decoration-none fw-bold">Đăng ký tại đây</a></p>
            <a href="index.php" class="text-muted text-decoration-none auth-link-small">&larr; Quay lại trang chủ</a>
        </div>
    </div>
</div>
</body>
</html>