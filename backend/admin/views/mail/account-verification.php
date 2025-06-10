<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 200px;
            height: auto;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #17a2b8;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
        .verification-code {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="Logo" class="logo">
    </div>

    <div class="content">
        <h2>Xác thực tài khoản</h2>

        <p>Xin chào <?php echo $username; ?>,</p>

        <p>Cảm ơn bạn đã đăng ký tài khoản tại <?php echo APP_NAME; ?>. Để hoàn tất quá trình đăng ký, vui lòng xác thực tài khoản của bạn bằng mã xác thực dưới đây:</p>

        <div class="verification-code">
            <?php echo $verification_code; ?>
        </div>

        <div class="warning">
            <p><strong>Lưu ý:</strong> Mã xác thực này sẽ hết hạn sau 24 giờ.</p>
        </div>

        <p>Hoặc bạn có thể nhấp vào nút bên dưới để xác thực tài khoản:</p>

        <a href="<?php echo $verification_link; ?>" class="button">Xác thực tài khoản</a>

        <p>Nếu bạn không thực hiện đăng ký tài khoản này, vui lòng bỏ qua email này.</p>

        <p>Trân trọng,<br>Đội ngũ <?php echo APP_NAME; ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        <p>Email: <?php echo MAIL_FROM_ADDRESS; ?></p>
    </div>
</body>
</html>