<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lời mời tham gia cuộc thi - <?php echo APP_NAME; ?></title>
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
        .contest-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .prize-info {
            background-color: #fff3e0;
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
        <h2>Lời mời tham gia cuộc thi</h2>

        <p>Xin chào <?php echo $username; ?>,</p>

        <p>Chúng tôi rất vui mừng mời bạn tham gia cuộc thi mới tại <?php echo APP_NAME; ?>.</p>

        <div class="contest-info">
            <h3><?php echo $contest_name; ?></h3>
            <p><strong>Thời gian:</strong> <?php echo $contest_start_date; ?> - <?php echo $contest_end_date; ?></p>
            <p><strong>Mô tả:</strong> <?php echo $contest_description; ?></p>
        </div>

        <div class="prize-info">
            <h3>Giải thưởng</h3>
            <p><?php echo $prize_description; ?></p>
        </div>

        <p>Để tham gia cuộc thi, vui lòng nhấp vào nút bên dưới:</p>

        <a href="<?php echo $contest_link; ?>" class="button">Tham gia ngay</a>

        <p>Hạn chót đăng ký: <?php echo $registration_deadline; ?></p>

        <p>Nếu bạn có bất kỳ câu hỏi nào về cuộc thi, đừng ngần ngại liên hệ với chúng tôi.</p>

        <p>Trân trọng,<br>Đội ngũ <?php echo APP_NAME; ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        <p>Email: <?php echo MAIL_FROM_ADDRESS; ?></p>
    </div>
</body>
</html>