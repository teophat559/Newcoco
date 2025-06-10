<?php
require_once __DIR__ . '/../config/config.php';

$error_code = $_GET['code'] ?? '404';
$error_message = $_GET['message'] ?? 'Page not found';

switch ($error_code) {
    case '403':
        $title = 'Access Denied';
        break;
    case '404':
        $title = 'Page Not Found';
        break;
    case '500':
        $title = 'Server Error';
        break;
    default:
        $title = 'Error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="error-page">
    <div class="error-container">
        <div class="error-box">
            <h1><?php echo $error_code; ?></h1>
            <h2><?php echo $title; ?></h2>
            <p><?php echo $error_message; ?></p>
            <a href="/admin/dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>