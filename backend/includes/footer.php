<footer class="bg-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><?php echo SITE_NAME; ?></h5>
                <p class="text-muted">Hệ thống quản lý cuộc thi trực tuyến, cho phép người dùng tạo và tham gia các cuộc thi, bình chọn và theo dõi kết quả.</p>
            </div>
            <div class="col-md-4">
                <h5>Liên kết</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li><a href="contests.php" class="text-decoration-none text-muted">Cuộc thi</a></li>
                    <li><a href="about.php" class="text-decoration-none text-muted">Giới thiệu</a></li>
                    <li><a href="contact.php" class="text-decoration-none text-muted">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Liên hệ</h5>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-envelope"></i> <?php echo SMTP_USER; ?></li>
                    <li><i class="bi bi-telephone"></i> <?php echo SITE_PHONE; ?></li>
                    <li><i class="bi bi-geo-alt"></i> <?php echo SITE_ADDRESS; ?></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="text-center text-muted">
            <small>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</small>
        </div>
    </div>
</footer>