<?php
session_start();
require_once '../config.php';

// Kiểm tra xem có phải admin không
$is_admin = isset($_SESSION['admin_id']);

// Thông tin lỗi
$error_info = [
    'title' => 'Yêu cầu xác thực',
    'message' => 'Bạn cần đăng nhập để truy cập trang này.',
    'reasons' => [
        'Bạn chưa đăng nhập vào hệ thống',
        'Phiên đăng nhập của bạn đã hết hạn',
        'Bạn cần quyền admin để truy cập',
        'Tài khoản của bạn đã bị khóa'
    ],
    'actions' => [
        'Đăng nhập vào hệ thống',
        'Liên hệ quản trị viên',
        'Kiểm tra lại quyền truy cập',
        'Xóa cache trình duyệt'
    ],
    'contact_email' => 'support@example.com',
    'contact_phone' => '0123 456 789',
    'login_url' => 'login.php',
    'return_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu xác thực - <?php echo SITE_NAME; ?></title>
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
            animation: bounce 2s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
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
            color: #ffc107;
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
            color: #ffc107;
        }
        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .login-form {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-floating {
            margin-bottom: 1rem;
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

        <?php if (!$is_admin): ?>
            <div class="login-form">
                <form action="<?php echo $error_info['login_url']; ?>" method="post">
                    <input type="hidden" name="return_url" value="<?php echo $error_info['return_url']; ?>">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                        <label for="password">Mật khẩu</label>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Đăng nhập
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div class="error-details">
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
                <a href="javascript:history.back()" class="btn btn-outline-warning">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại
                </a>
                <?php if ($is_admin): ?>
                    <a href="index.php" class="btn btn-warning">
                        <i class="bi bi-house"></i>
                        Trang chủ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>