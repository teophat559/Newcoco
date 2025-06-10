<?php
session_start();
require_once '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng dẫn sử dụng - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #f8f9fa;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        .help-section {
            margin-bottom: 2rem;
        }
        .help-section h3 {
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .help-section .card {
            margin-bottom: 1rem;
        }
        .help-section .card-header {
            background-color: #f8f9fa;
            border-bottom: none;
        }
        .help-section .card-body {
            padding: 1.5rem;
        }
        .help-section .list-group-item {
            border: none;
            padding: 0.5rem 0;
        }
        .help-section .list-group-item i {
            color: #0d6efd;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../images/logo.png" alt="<?php echo SITE_NAME; ?>" height="30" class="d-inline-block align-text-top">
                Admin Panel
            </a>
            <div class="d-flex">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-white text-decoration-none" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">Thông tin cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-house-door"></i>
                                Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contests.php">
                                <i class="bi bi-trophy"></i>
                                Cuộc thi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="bi bi-people"></i>
                                Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contestants.php">
                                <i class="bi bi-person-badge"></i>
                                Thí sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="votes.php">
                                <i class="bi bi-hand-thumbs-up"></i>
                                Bình chọn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="bi bi-gear"></i>
                                Cài đặt
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Hướng dẫn sử dụng</h1>
                </div>

                <!-- Quản lý cuộc thi -->
                <div class="help-section">
                    <h3><i class="bi bi-trophy"></i> Quản lý cuộc thi</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tạo cuộc thi mới</h5>
                        </div>
                        <div class="card-body">
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Truy cập trang "Cuộc thi"</li>
                                <li class="list-group-item">Nhấn nút "Thêm cuộc thi mới"</li>
                                <li class="list-group-item">Điền đầy đủ thông tin:
                                    <ul>
                                        <li>Tên cuộc thi</li>
                                        <li>Mô tả</li>
                                        <li>Hình ảnh đại diện</li>
                                        <li>Thời gian bắt đầu và kết thúc</li>
                                        <li>Trạng thái (active/inactive)</li>
                                    </ul>
                                </li>
                                <li class="list-group-item">Nhấn "Lưu" để tạo cuộc thi</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Quản lý người dùng -->
                <div class="help-section">
                    <h3><i class="bi bi-people"></i> Quản lý người dùng</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Các thao tác với người dùng</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="bi bi-eye"></i>
                                    Xem thông tin chi tiết người dùng
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-toggle-on"></i>
                                    Kích hoạt/vô hiệu hóa tài khoản
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-trash"></i>
                                    Xóa tài khoản (chỉ khi người dùng chưa tham gia cuộc thi)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quản lý thí sinh -->
                <div class="help-section">
                    <h3><i class="bi bi-person-badge"></i> Quản lý thí sinh</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Duyệt thí sinh</h5>
                        </div>
                        <div class="card-body">
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Truy cập trang "Thí sinh"</li>
                                <li class="list-group-item">Xem danh sách thí sinh đăng ký</li>
                                <li class="list-group-item">Kiểm tra thông tin và hình ảnh</li>
                                <li class="list-group-item">Chọn một trong các hành động:
                                    <ul>
                                        <li>Duyệt: Thí sinh được tham gia cuộc thi</li>
                                        <li>Từ chối: Thí sinh không được tham gia</li>
                                        <li>Xóa: Xóa đăng ký của thí sinh</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Quản lý bình chọn -->
                <div class="help-section">
                    <h3><i class="bi bi-hand-thumbs-up"></i> Quản lý bình chọn</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Theo dõi và quản lý bình chọn</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="bi bi-graph-up"></i>
                                    Xem thống kê số lượt bình chọn
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-clock-history"></i>
                                    Theo dõi lịch sử bình chọn
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Phát hiện và xử lý bình chọn bất thường
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Cài đặt hệ thống -->
                <div class="help-section">
                    <h3><i class="bi bi-gear"></i> Cài đặt hệ thống</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Các cài đặt quan trọng</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="bi bi-info-circle"></i>
                                    Thông tin website (tên, mô tả, liên hệ)
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-envelope"></i>
                                    Cấu hình email (SMTP)
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-shield-check"></i>
                                    Cài đặt bảo mật (giới hạn bình chọn, thời gian chờ)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sao lưu và khôi phục -->
                <div class="help-section">
                    <h3><i class="bi bi-database"></i> Sao lưu và khôi phục</h3>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Hướng dẫn sao lưu dữ liệu</h5>
                        </div>
                        <div class="card-body">
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Truy cập trang "Sao lưu & Khôi phục"</li>
                                <li class="list-group-item">Nhấn "Tạo backup" để sao lưu dữ liệu</li>
                                <li class="list-group-item">Tải file backup về máy để lưu trữ</li>
                                <li class="list-group-item">Để khôi phục:
                                    <ul>
                                        <li>Upload file backup lên server</li>
                                        <li>Nhấn nút khôi phục tương ứng</li>
                                        <li>Xác nhận khôi phục</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>