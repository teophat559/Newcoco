<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Lấy thông tin bảo trì từ cấu hình
$maintenance_info = [
    'title' => 'Hệ thống đang bảo trì',
    'message' => 'Chúng tôi đang nâng cấp hệ thống để phục vụ bạn tốt hơn.',
    'estimated_time' => '2 giờ',
    'start_time' => date('Y-m-d H:i:s'),
    'end_time' => date('Y-m-d H:i:s', strtotime('+2 hours')),
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảo trì hệ thống - <?php echo SITE_NAME; ?></title>
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
        .maintenance-container {
            text-align: center;
            padding: 2rem;
            max-width: 800px;
        }
        .maintenance-icon {
            font-size: 5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
            animation: wrench 2.5s ease infinite;
        }
        @keyframes wrench {
            0% { transform: rotate(-12deg); }
            8% { transform: rotate(12deg); }
            10% { transform: rotate(24deg); }
            18% { transform: rotate(-24deg); }
            20% { transform: rotate(-24deg); }
            28% { transform: rotate(24deg); }
            30% { transform: rotate(24deg); }
            38% { transform: rotate(-24deg); }
            40% { transform: rotate(-24deg); }
            48% { transform: rotate(24deg); }
            50% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        .maintenance-title {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .maintenance-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .maintenance-details {
            background-color: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .maintenance-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-item {
            text-align: left;
        }
        .info-item h6 {
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .info-item p {
            color: #6c757d;
            margin-bottom: 0;
        }
        .progress {
            height: 10px;
            margin: 2rem 0;
        }
        .progress-bar {
            animation: progress 2s ease-in-out infinite;
        }
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; }
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
            color: #0d6efd;
        }
        .admin-login {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="bi bi-tools"></i>
        </div>
        <h1 class="maintenance-title"><?php echo $maintenance_info['title']; ?></h1>
        <p class="maintenance-message"><?php echo $maintenance_info['message']; ?></p>

        <div class="maintenance-details">
            <div class="maintenance-info">
                <div class="info-item">
                    <h6>Thời gian bắt đầu</h6>
                    <p><?php echo date('d/m/Y H:i', strtotime($maintenance_info['start_time'])); ?></p>
                </div>
                <div class="info-item">
                    <h6>Thời gian kết thúc dự kiến</h6>
                    <p><?php echo date('d/m/Y H:i', strtotime($maintenance_info['end_time'])); ?></p>
                </div>
                <div class="info-item">
                    <h6>Thời gian bảo trì</h6>
                    <p><?php echo $maintenance_info['estimated_time']; ?></p>
                </div>
            </div>

            <div class="progress">
                <div class="progress-bar bg-primary" role="progressbar"></div>
            </div>

            <div class="contact-info">
                <h5>Liên hệ hỗ trợ</h5>
                <div class="contact-methods">
                    <a href="mailto:<?php echo $maintenance_info['contact_email']; ?>" class="contact-method">
                        <i class="bi bi-envelope"></i>
                        <?php echo $maintenance_info['contact_email']; ?>
                    </a>
                    <a href="tel:<?php echo $maintenance_info['contact_phone']; ?>" class="contact-method">
                        <i class="bi bi-telephone"></i>
                        <?php echo $maintenance_info['contact_phone']; ?>
                    </a>
                </div>
            </div>

            <?php if ($is_admin): ?>
                <div class="admin-login">
                    <a href="index.php" class="btn btn-primary">
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