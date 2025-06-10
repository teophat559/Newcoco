<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả cuộc thi - <?php echo APP_NAME; ?></title>
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
        .result-info {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .winner {
            background-color: #fff3e0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .congrats {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="Logo" class="logo">
    </div>

    <div class="content">
        <h2>Kết quả cuộc thi</h2>

        <p>Xin chào <?php echo $username; ?>,</p>

        <p>Cuộc thi <?php echo $contest_name; ?> đã kết thúc và chúng tôi rất vui mừng thông báo kết quả.</p>

        <div class="result-info">
            <h3>Thông tin cuộc thi</h3>
            <p><strong>Tên cuộc thi:</strong> <?php echo $contest_name; ?></p>
            <p><strong>Thời gian:</strong> <?php echo $contest_start_date; ?> - <?php echo $contest_end_date; ?></p>
            <p><strong>Tổng số người tham gia:</strong> <?php echo $total_participants; ?></p>
        </div>

        <div class="winner">
            <h3>Kết quả của bạn</h3>
            <p><strong>Vị trí:</strong> <?php echo $position; ?></p>
            <p><strong>Số điểm:</strong> <?php echo $score; ?></p>
            <?php if ($is_winner): ?>
            <div class="congrats">
                Chúc mừng bạn đã chiến thắng!
            </div>
            <p><strong>Giải thưởng:</strong> <?php echo $prize; ?></p>
            <?php endif; ?>
        </div>

        <p>Để xem kết quả chi tiết và bảng xếp hạng, vui lòng nhấp vào nút bên dưới:</p>

        <a href="<?php echo $result_link; ?>" class="button">Xem kết quả chi tiết</a>

        <p>Chúng tôi xin chân thành cảm ơn sự tham gia của bạn và mong đợi gặp lại bạn trong các cuộc thi tiếp theo!</p>

        <p>Trân trọng,<br>Đội ngũ <?php echo APP_NAME; ?></p>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        <p>Email: <?php echo MAIL_FROM_ADDRESS; ?></p>
    </div>
</body>
</html>