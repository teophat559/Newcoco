<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo mới - <?php echo APP_NAME; ?></title>
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
            background-color: #28a745;
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
        .notification {
            background-color: #e8f5e9;
            padding: 15px;
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
        <h2>Thông báo mới</h2>

        <p>Xin chào <?php echo $username; ?>,</p>

        <div class="notification">
            <h3><?php echo $notification_title; ?></h3>
            <p><?php echo $notification_message; ?></p>
        </div>

        <p>Để xem chi tiết, vui lòng nhấp vào nút bên dưới:</p>

        <a href="<?php echo $notification_link; ?>" class="button">Xem chi tiết</a>

        <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.</p>

        <p>Trân trọng,<br>Đội ngũ <?php echo APP_NAME; ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        <p>Email: <?php echo MAIL_FROM_ADDRESS; ?></p>
    </div>
</body>
</html>