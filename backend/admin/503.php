<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Thông tin lỗi
$error_info = [
    'title' => 'Dịch vụ không khả dụng',
    'message' => 'Hệ thống đang trong quá trình bảo trì hoặc nâng cấp.',
    'reasons' => [
        'Bảo trì hệ thống định kỳ',
        'Nâng cấp cơ sở dữ liệu',
        'Cập nhật phần mềm',
        'Khắc phục sự cố'
    ],
    'actions' => [
        'Quay lại sau vài phút',
        'Kiểm tra trạng thái hệ thống',
        'Theo dõi thông báo',
        'Liên hệ hỗ trợ kỹ thuật'
    ],
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789',
    'error_id' => uniqid('ERR_'),
    'timestamp' => date('Y-m-d H:i:s'),
    'estimated_time' => '30 phút',
    'maintenance_type' => 'Bảo trì định kỳ',
    'affected_services' => [
        'Quản lý cuộc thi',
        'Đăng ký thí sinh',
        'Bình chọn',
        'Báo cáo thống kê'
    ]
];

// Ghi log lỗi
$log_message = sprintf(
    "Error ID: %s\nTime: %s\nUser: %s\nIP: %s\nMaintenance Type: %s\nEstimated Time: %s",
    $error_info['error_id'],
    $error_info['timestamp'],
    $is_admin ? 'Admin' : 'Guest',
    $_SERVER['REMOTE_ADDR'],
    $error_info['maintenance_type'],
    $error_info['estimated_time']
);
error_log($log_message, 3, '../logs/error.log');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch vụ không khả dụng - <?php echo SITE_NAME; ?></title>
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
            color: #ffc107;
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
        .error-title {
            font-size: 2.5rem;
            color: #ffc107;
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
        .error-section {
            margin-bottom: 2rem;
        }
        .error-section h5 {
            color: #495057;
            margin-bottom: 1rem;
        }
        .error-section ul {
            list-style: none;
            padding-left: 0;
        }
        .error-section li {
            color: #6c757d;
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        .error-section li:before {
            content: "•";
            color: #6c757d;
            position: absolute;
            left: 0;
        }
        .error-info {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.9rem;
            color: #6c757d;
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
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .maintenance-info {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .maintenance-info h5 {
            color: #856404;
            margin-bottom: 0.5rem;
        }
        .maintenance-info p {
            color: #856404;
            margin-bottom: 0;
        }
        .affected-services {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
        }
        .service-badge {
            background-color: #fff3cd;
            color: #856404;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-tools"></i>
        </div>
        <h1 class="error-title"><?php echo $error_info['title']; ?></h1>
        <p class="error-message"><?php echo $error_info['message']; ?></p>

        <div class="error-details">
            <div class="maintenance-info">
                <h5>Thông tin bảo trì</h5>
                <p>Loại bảo trì: <?php echo $error_info['maintenance_type']; ?></p>
                <p>Thời gian dự kiến: <?php echo $error_info['estimated_time']; ?></p>
                <div class="affected-services">
                    <?php foreach ($error_info['affected_services'] as $service): ?>
                        <span class="service-badge"><?php echo $service; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="error-info">
                <div>Error ID: <?php echo $error_info['error_id']; ?></div>
                <div>Time: <?php echo $error_info['timestamp']; ?></div>
                <div>Maintenance Type: <?php echo $error_info['maintenance_type']; ?></div>
                <div>Estimated Time: <?php echo $error_info['estimated_time']; ?></div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="error-section">
                        <h5>Nguyên nhân có thể:</h5>
                        <ul>
                            <?php foreach ($error_info['reasons'] as $reason): ?>
                                <li><?php echo $reason; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="error-section">
                        <h5>Bạn có thể thử:</h5>
                        <ul>
                            <?php foreach ($error_info['actions'] as $action): ?>
                                <li><?php echo $action; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h5>Liên hệ hỗ trợ kỹ thuật</h5>
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
                <button onclick="window.location.reload()" class="btn btn-outline-warning">
                    <i class="bi bi-arrow-clockwise"></i>
                    Kiểm tra lại
                </button>
                <?php if ($is_admin): ?>
                    <a href="index.php" class="btn btn-warning">
                        <i class="bi bi-house"></i>
                        Trang chủ
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-warning">
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