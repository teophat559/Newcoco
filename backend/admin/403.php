<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Thông tin lỗi
$error_info = [
    'title' => 'Truy cập bị từ chối',
    'message' => 'Bạn không có quyền truy cập vào trang này.',
    'reasons' => [
        'Bạn chưa đăng nhập vào hệ thống',
        'Tài khoản của bạn không có quyền truy cập',
        'Phiên đăng nhập của bạn đã hết hạn',
        'IP của bạn đã bị chặn'
    ],
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truy cập bị từ chối - <?php echo SITE_NAME; ?></title>
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
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 800px;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: shake 2s ease-in-out infinite;
        }
        @keyframes shake {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(-10deg); }
            20% { transform: rotate(10deg); }
            30% { transform: rotate(-10deg); }
            40% { transform: rotate(10deg); }
            50% { transform: rotate(-5deg); }
            60% { transform: rotate(5deg); }
            70% { transform: rotate(-5deg); }
            80% { transform: rotate(5deg); }
            90% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }
        .error-title {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .error-details {
            background-color: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .error-reasons {
            text-align: left;
            margin-bottom: 2rem;
        }
        .error-reasons h5 {
            color: #495057;
            margin-bottom: 1rem;
        }
        .error-reasons ul {
            list-style: none;
            padding-left: 0;
        }
        .error-reasons li {
            color: #6c757d;
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        .error-reasons li:before {
            content: "•";
            color: #dc3545;
            position: absolute;
            left: 0;
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
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 class="error-title"><?php echo $error_info['title']; ?></h1>
        <p class="error-message"><?php echo $error_info['message']; ?></p>

        <div class="error-details">
            <div class="error-reasons">
                <h5>Nguyên nhân có thể:</h5>
                <ul>
                    <?php foreach ($error_info['reasons'] as $reason): ?>
                        <li><?php echo $reason; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="contact-info">
                <h5>Liên hệ hỗ trợ</h5>
                <div class="contact-methods">
                    <a href="mailto:<?php echo $error_info['contact_email']; ?>" class="contact-method">
                        <i class="bi bi-envelope"></i>
                        <?php echo $error_info['contact_email']; ?>
                    </a>
                    <a href="tel:<?php echo $error_info['contact_phone']; ?>" class="contact-method">
                        <i class="bi bi-telephone"></i>
                        <?php echo $error_info['contact_phone']; ?>
                    </a>
                </div>
            </div>

            <div class="action-buttons">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại
                </a>
                <?php if ($is_admin): ?>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house"></i>
                        Trang chủ
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>