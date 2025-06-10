<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Thông tin lỗi
$error_info = [
    'title' => 'Lỗi cổng không hợp lệ',
    'message' => 'Máy chủ đang gặp vấn đề khi xử lý yêu cầu của bạn.',
    'reasons' => [
        'Máy chủ upstream không phản hồi',
        'Kết nối đến máy chủ bị gián đoạn',
        'Máy chủ đang quá tải',
        'Lỗi cấu hình proxy'
    ],
    'actions' => [
        'Làm mới trang sau vài giây',
        'Kiểm tra kết nối mạng',
        'Xóa cache trình duyệt',
        'Liên hệ hỗ trợ kỹ thuật'
    ],
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789',
    'error_id' => uniqid('ERR_'),
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'host' => $_SERVER['HTTP_HOST'],
        'port' => $_SERVER['SERVER_PORT'],
        'protocol' => $_SERVER['SERVER_PROTOCOL']
    ]
];

// Ghi log lỗi
$log_message = sprintf(
    "Error ID: %s\nTime: %s\nUser: %s\nIP: %s\nHost: %s\nPort: %s\nProtocol: %s",
    $error_info['error_id'],
    $error_info['timestamp'],
    $is_admin ? 'Admin' : 'Guest',
    $_SERVER['REMOTE_ADDR'],
    $error_info['server_info']['host'],
    $error_info['server_info']['port'],
    $error_info['server_info']['protocol']
);
error_log($log_message, 3, '../logs/error.log');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi cổng không hợp lệ - <?php echo SITE_NAME; ?></title>
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
            0% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
            100% { transform: translateX(0); }
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
        .server-info {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .server-info h5 {
            color: #721c24;
            margin-bottom: 0.5rem;
        }
        .server-info p {
            color: #721c24;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h1 class="error-title"><?php echo $error_info['title']; ?></h1>
        <p class="error-message"><?php echo $error_info['message']; ?></p>

        <div class="error-details">
            <div class="server-info">
                <h5>Thông tin máy chủ</h5>
                <p>Host: <?php echo $error_info['server_info']['host']; ?></p>
                <p>Port: <?php echo $error_info['server_info']['port']; ?></p>
                <p>Protocol: <?php echo $error_info['server_info']['protocol']; ?></p>
            </div>

            <div class="error-info">
                <div>Error ID: <?php echo $error_info['error_id']; ?></div>
                <div>Time: <?php echo $error_info['timestamp']; ?></div>
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
                <button onclick="window.location.reload()" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-clockwise"></i>
                    Thử lại
                </button>
                <?php if ($is_admin): ?>
                    <a href="index.php" class="btn btn-danger">
                        <i class="bi bi-house"></i>
                        Trang chủ
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-danger">
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