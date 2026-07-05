<?php
include 'header.php';
require_once 'config/sys_config.php';

$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đoạn này xử lý nhận lời nhắn từ khách (ní có thể phát triển lưu vào DB hoặc gửi mail sau)
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (!empty($fullname) && !empty($message)) {
        $success_msg = "Cảm ơn $fullname đã gửi phản hồi! CaMau Food sẽ liên hệ lại sớm nhất.";
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="text-warning fw-bold text-uppercase">📞 LIÊN HỆ VỚI CHÚNG TÔI 📞</h2>
        <p class="text-muted">Mọi thắc mắc hay đóng góp ý kiến vui lòng để lại thông tin bên dưới</p>
        <hr class="w-25 mx-auto text-warning contact-divider">
    </div>

    <div class="row g-4">
        <!-- Cột trái: Thông tin liên hệ & Bản đồ -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm h-100 border-0 bg-white">
                <h4 class="fw-bold text-success mb-3">🏡 CaMau Food</h4>
                <p class="text-muted mb-2"><strong>📍 Địa chỉ:</strong> Phường 5, Thành phố Cà Mau, Tỉnh Cà Mau</p>
                <p class="text-muted mb-2"><strong>📞 Điện thoại:</strong> 0942398774</p>
                <p class="text-muted mb-4"><strong>✉️ Email:</strong> 24210501030@student.bdu.edu.vn</p>
                
                <!-- Nhúng bản đồ Google Map tĩnh/động địa phận Cà Mau -->
                <div class="rounded overflow-hidden shadow-sm contact-map-frame">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62900.413723382725!2d105.11504958178128!3d9.174668270830491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a149a37e5df07d%3A0x6bfe760777fd65bc!2zVHAuIEPDoCBNYXUsIEPDoCBNYXUsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1710000000000!5m2!1svi!2s" 
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <!-- Cột phải: Form Gửi Lời Nhắn -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm h-100 border-0 bg-white">
                <h4 class="fw-bold text-warning mb-3">✉️ GỬI PHẢN HỒI</h4>
                
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success small"><?= $success_msg ?></div>
                <?php endif; ?>

                <form action="contact.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên của bạn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nội dung lời nhắn</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Viết phản hồi hoặc góp ý của bạn tại đây..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning text-white fw-bold w-100">Gửi lời nhắn ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>