<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng đến với <?php echo APP_NAME; ?></title>
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
            background-color: #007bff;
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
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="Logo" class="logo">
    </div>

    <div class="content">
        <h2>Chào mừng <?php echo $username; ?>!</h2>

        <p>Chào mừng bạn đến với <?php echo APP_NAME; ?> - Hệ thống quản lý cuộc thi trực tuyến.</p>

        <p>Chúng tôi rất vui mừng khi bạn đã tham gia cùng chúng tôi. Với tài khoản của mình, bạn có thể:</p>

        <ul>
            <li>Tham gia các cuộc thi</li>
            <li>Bình chọn cho thí sinh</li>
            <li>Theo dõi kết quả</li>
            <li>Nhận thông báo mới nhất</li>
        </ul>

        <p>Để bắt đầu, hãy đăng nhập vào tài khoản của bạn:</p>

        <a href="<?php echo APP_URL; ?>/login.php" class="button">Đăng nhập ngay</a>

        <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.</p>

        <p>Trân trọng,<br>Đội ngũ <?php echo APP_NAME; ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        <p>Email: <?php echo MAIL_FROM_ADDRESS; ?></p>
    </div>
</body>
</html>