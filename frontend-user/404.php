<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Log error details
$requested_url = $_SERVER['REQUEST_URI'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
$error_message = "404 Error - URL: $requested_url, IP: $ip_address, User: $user_id, User Agent: $user_agent";
error_log($error_message);

// Set page title
$page_title = '404 - Không tìm thấy trang';

// Include header
require_once __DIR__ . '/includes/header.php';
?>
<div class="error-page">
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Không tìm thấy trang</h2>
        <p class="error-message">
            Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.<br>
            Vui lòng kiểm tra lại URL hoặc quay về trang chủ.
        </p>
        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
        <div class="error-info">
            <small>
                Mã lỗi: <?php echo htmlspecialchars($error_message); ?><br>
                URL: <?php echo htmlspecialchars($requested_url); ?><br>
                User ID: <?php echo htmlspecialchars($user_id); ?>
            </small>
        </div>
        <?php endif; ?>
        <div class="error-actions">
            <a href="<?php echo SITE_URL; ?>" class="error-btn error-btn-primary">
                <i class="fas fa-home"></i>
                Về trang chủ
            </a>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="error-btn error-btn-outline">
                <i class="fas fa-envelope"></i>
                Liên hệ hỗ trợ
            </a>
        </div>
        <div class="error-contact">
            <p>
                Cần hỗ trợ? Hãy liên hệ với chúng tôi:<br>
                <a href="mailto:<?php echo SITE_EMAIL; ?>">
                    <i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?>
                </a><br>
                <a href="tel:<?php echo SITE_PHONE; ?>">
                    <i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?>
                </a>
            </p>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
