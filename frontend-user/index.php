<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Thiết lập trang hiện tại
$current_page = 'home';
$page_title = 'Trang chủ';

// Lấy danh sách cuộc thi
$active_contests = get_active_contests();
$upcoming_contests = get_upcoming_contests();
$featured_contests = get_featured_contests();
$popular_contests = get_popular_contests();

// Lấy thống kê
$stats = get_site_statistics();

$content = '<!-- Hero Section -->
<div class="hero-section bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">' . SITE_NAME . '</h1>
                <p class="lead mb-4">' . SITE_DESCRIPTION . '</p>
                <div class="d-flex gap-3">
                    <a href="' . SITE_URL . '/contests" class="btn btn-light btn-lg">
                        <i class="fas fa-trophy me-2"></i>
                        Xem cuộc thi
                    </a>';

if (!is_logged_in()) {
    $content .= '<a href="' . SITE_URL . '/auth/register.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>
                        Đăng ký ngay
                    </a>';
}

$content .= '</div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="' . SITE_URL . '/assets/images/hero-image.svg" alt="Hero Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-trophy fa-2x mb-3"></i>
                    <h3 class="stat-number">' . format_number($stats['total_contests']) . '</h3>
                    <p class="stat-label">Cuộc thi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-3"></i>
                    <h3 class="stat-number">' . format_number($stats['total_contestants']) . '</h3>
                    <p class="stat-label">Thí sinh</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-vote-yea fa-2x mb-3"></i>
                    <h3 class="stat-number">' . format_number($stats['total_votes']) . '</h3>
                    <p class="stat-label">Lượt bình chọn</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-friends fa-2x mb-3"></i>
                    <h3 class="stat-number">' . format_number($stats['total_users']) . '</h3>
                    <p class="stat-label">Thành viên</p>
                </div>
            </div>
        </div>
    </div>
</div>';

if (!empty($featured_contests)) {
    $content .= '<!-- Featured Contests Section -->
<div class="container mb-5">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">
            <i class="fas fa-star text-warning me-2"></i>
            Cuộc thi nổi bật
        </h2>
        <a href="' . SITE_URL . '/contests?filter=featured" class="btn btn-outline-primary">
            Xem tất cả
            <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';

    foreach ($featured_contests as $contest) {
        $content .= '<div class="col">
            <div class="card h-100 contest-card featured">';

        if ($contest['image']) {
            $content .= '<div class="contest-image-wrapper">
                    <img src="' . SITE_URL . '/uploads/contests/' . $contest['image'] . '"
                         class="card-img-top contest-image"
                         alt="' . sanitize_output($contest['title']) . '">
                    <div class="featured-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>';
        }

        $content .= '<div class="card-body">
                <h5 class="card-title contest-title">
                    ' . sanitize_output($contest['title']) . '
                </h5>
                <p class="card-text contest-description">
                    ' . sanitize_output(substr($contest['description'], 0, 150)) . '...
                </p>
                <div class="contest-meta">
                    <span class="me-3">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ' . format_date($contest['end_date']) . '
                    </span>
                    <span>
                        <i class="fas fa-users me-1"></i>
                        ' . format_number($contest['contestant_count']) . ' thí sinh
                    </span>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <a href="' . SITE_URL . '/contests/view.php?id=' . $contest['id'] . '"
                   class="btn btn-primary w-100">
                    Xem chi tiết
                </a>
            </div>
        </div>';
    }

    $content .= '</div>
</div>';
}

$content .= '<!-- Active Contests Section -->
<div class="container mb-5">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">
            <i class="fas fa-fire text-danger me-2"></i>
            Cuộc thi đang diễn ra
        </h2>
        <a href="' . SITE_URL . '/contests?filter=active" class="btn btn-outline-primary">
            Xem tất cả
            <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';

if (empty($active_contests)) {
    $content .= '<div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Hiện tại không có cuộc thi nào đang diễn ra.
            </div>
        </div>';
} else {
    foreach ($active_contests as $contest) {
        $content .= '<div class="col">
            <div class="card h-100 contest-card">';

        if ($contest['image']) {
            $content .= '<div class="contest-image-wrapper">
                    <img src="' . SITE_URL . '/uploads/contests/' . $contest['image'] . '"
                         class="card-img-top contest-image"
                         alt="' . sanitize_output($contest['title']) . '">
                    <div class="contest-status active">
                        Đang diễn ra
                    </div>
                </div>';
        }

        $content .= '<div class="card-body">
                <h5 class="card-title contest-title">
                    ' . sanitize_output($contest['title']) . '
                </h5>
                <p class="card-text contest-description">
                    ' . sanitize_output(substr($contest['description'], 0, 150)) . '...
                </p>
                <div class="contest-meta">
                    <span class="me-3">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ' . format_date($contest['end_date']) . '
                    </span>
                    <span>
                        <i class="fas fa-users me-1"></i>
                        ' . format_number($contest['contestant_count']) . ' thí sinh
                    </span>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <a href="' . SITE_URL . '/contests/view.php?id=' . $contest['id'] . '"
                   class="btn btn-primary w-100">
                    Xem chi tiết
                </a>
            </div>
        </div>';
    }
}

$content .= '</div>
</div>';

require_once __DIR__ . '/includes/header.php';
echo $content;
require_once __DIR__ . '/includes/footer.php';