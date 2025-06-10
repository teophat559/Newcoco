<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Thông tin hệ thống offline
$offline_info = [
    'title' => 'Hệ thống đang offline',
    'message' => 'Xin lỗi, hệ thống hiện đang không hoạt động. Vui lòng thử lại sau.',
    'reason' => 'Chúng tôi đang gặp một số vấn đề kỹ thuật và đang nỗ lực khắc phục.',
    'estimated_time' => '30 phút',
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789',
    'social_media' => [
        'facebook' => 'https://facebook.com/example',
        'twitter' => 'https://twitter.com/example',
        'instagram' => 'https://instagram.com/example'
    ]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống offline - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .offline-container {
            text-align: center;
            padding: 2rem;
            max-width: 800px;
        }
        .offline-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .offline-title {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .offline-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .offline-details {
            background-color: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .offline-info {
            margin-bottom: 2rem;
        }
        .offline-info h6 {
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .offline-info p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }
        .contact-info h5 {
            color: #495057;
            margin-bottom: 1rem;
        }
        .contact-methods {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .contact-method {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            text-decoration: none;
        }
        .contact-method:hover {
            color: #dc3545;
        }
        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        .social-link {
            color: #6c757d;
            font-size: 1.5rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .social-link:hover {
            color: #dc3545;
        }
        .admin-login {
            margin-top: 2rem;
        }
        .retry-button {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h1 class="offline-title"><?php echo $offline_info['title']; ?></h1>
        <p class="offline-message"><?php echo $offline_info['message']; ?></p>

        <div class="offline-details">
            <div class="offline-info">
                <h6>Lý do</h6>
                <p><?php echo $offline_info['reason']; ?></p>
                <h6>Thời gian khắc phục dự kiến</h6>
                <p><?php echo $offline_info['estimated_time']; ?></p>
            </div>

            <div class="contact-info">
                <h5>Liên hệ hỗ trợ</h5>
                <div class="contact-methods">
                    <a href="mailto:<?php echo $offline_info['contact_email']; ?>" class="contact-method">
                        <i class="bi bi-envelope"></i>
                        <?php echo $offline_info['contact_email']; ?>
                    </a>
                    <a href="tel:<?php echo $offline_info['contact_phone']; ?>" class="contact-method">
                        <i class="bi bi-telephone"></i>
                        <?php echo $offline_info['contact_phone']; ?>
                    </a>
                </div>

                <h5 class="mt-4">Theo dõi chúng tôi</h5>
                <div class="social-links">
                    <a href="<?php echo $offline_info['social_media']['facebook']; ?>" class="social-link" target="_blank">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="<?php echo $offline_info['social_media']['twitter']; ?>" class="social-link" target="_blank">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="<?php echo $offline_info['social_media']['instagram']; ?>" class="social-link" target="_blank">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>

            <div class="retry-button">
                <button onclick="window.location.reload()" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-clockwise"></i>
                    Thử lại
                </button>
            </div>

            <?php if ($is_admin): ?>
                <div class="admin-login">
                    <a href="index.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Truy cập trang quản trị
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>