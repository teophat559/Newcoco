<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảo trì hệ thống - <?php echo APP_NAME; ?></title>
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
        .maintenance-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ffc107;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .estimated-time {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        .contact-info {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
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
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        <h1>Hệ thống đang bảo trì</h1>
        <p>Xin lỗi vì sự bất tiện này. Chúng tôi đang thực hiện một số cập nhật để cải thiện trải nghiệm của bạn.</p>

        <div class="estimated-time">
            <strong>Thời gian dự kiến:</strong><br>
            <?php
            $stmt = $pdo->query("SELECT value FROM settings WHERE name = 'maintenance_message'");
            $message = $stmt->fetchColumn() ?: 'Chúng tôi sẽ quay lại sớm nhất có thể.';
            echo $message;
            ?>
        </div>

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