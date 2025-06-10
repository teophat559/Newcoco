<?php
require_once 'config.php';

// Get error details
$errorCode = $_GET['code'] ?? 500;
$errorMessage = $_GET['message'] ?? 'Đã xảy ra lỗi không xác định';

// Map error codes to messages
$errorMessages = [
    400 => 'Yêu cầu không hợp lệ',
    401 => 'Bạn cần đăng nhập để truy cập trang này',
    403 => 'Bạn không có quyền truy cập trang này',
    404 => 'Không tìm thấy trang yêu cầu',
    429 => 'Quá nhiều yêu cầu, vui lòng thử lại sau',
    500 => 'Lỗi máy chủ nội bộ',
    502 => 'Lỗi cổng kết nối',
    503 => 'Dịch vụ không khả dụng',
    504 => 'Hết thời gian chờ kết nối'
];

// Get friendly message
$friendlyMessage = $errorMessages[$errorCode] ?? $errorMessage;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi <?php echo $errorCode; ?> - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #dc3545;
            margin: 0;
            line-height: 1;
        }
        .error-message {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 1rem 0;
        }
        .error-details {
            color: #666;
            margin-bottom: 2rem;
        }
        .back-button {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .back-button:hover {
            background: #0056b3;
        }
        .contact-info {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?php echo $errorCode; ?></div>
        <h1 class="error-message"><?php echo $friendlyMessage; ?></h1>

        <?php if (DEBUG_MODE): ?>
        <div class="error-details">
            <p><?php echo htmlspecialchars($errorMessage); ?></p>
        </div>
        <?php endif; ?>

        <a href="/" class="back-button">Quay về trang chủ</a>

        <div class="contact-info">
            <p>Nếu bạn cần hỗ trợ, vui lòng liên hệ:</p>
            <p>
                Email: <a href="mailto:support@example.com">support@example.com</a><br>
                Hotline: <a href="tel:+84123456789">0123 456 789</a>
            </p>
        </div>
    </div>
</body>
</html>