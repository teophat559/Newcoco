<?php
session_start();
require_once '../config.php';

// Ghi log đăng xuất
if (isset($_SESSION['admin_id'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, ip_address, created_at)
            VALUES (?, 'logout', ?, NOW())
        ");
        $stmt->execute([$_SESSION['admin_id'], $_SERVER['REMOTE_ADDR']]);
    } catch (Exception $e) {
        // Bỏ qua lỗi khi ghi log
    }
}

// Xóa session
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit;