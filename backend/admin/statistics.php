<?php
session_start();
require_once '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy thống kê tổng quan
$stats = [
    'contests' => 0,
    'users' => 0,
    'contestants' => 0,
    'votes' => 0
];

try {
    // Tổng số cuộc thi
    $stmt = $pdo->query("SELECT COUNT(*) FROM contests");
    $stats['contests'] = $stmt->fetchColumn();

    // Tổng số người dùng
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['users'] = $stmt->fetchColumn();

    // Tổng số thí sinh
    $stmt = $pdo->query("SELECT COUNT(*) FROM contestants");
    $stats['contestants'] = $stmt->fetchColumn();

    // Tổng số lượt bình chọn
    $stmt = $pdo->query("SELECT COUNT(*) FROM votes");
    $stats['votes'] = $stmt->fetchColumn();

    // Top 5 cuộc thi có nhiều thí sinh nhất
    $stmt = $pdo->query("
        SELECT c.*, COUNT(ct.id) as contestant_count
        FROM contests c
        LEFT JOIN contestants ct ON c.id = ct.contest_id
        GROUP BY c.id
        ORDER BY contestant_count DESC
        LIMIT 5
    ");
    $top_contests = $stmt->fetchAll();

    // Top 5 thí sinh được bình chọn nhiều nhất
    $stmt = $pdo->query("
        SELECT ct.*, c.title as contest_title, u.name as user_name, COUNT(v.id) as vote_count
        FROM contestants ct
        JOIN contests c ON ct.contest_id = c.id
        JOIN users u ON ct.user_id = u.id
        LEFT JOIN votes v ON ct.id = v.contestant_id
        GROUP BY ct.id
        ORDER BY vote_count DESC
        LIMIT 5
    ");
    $top_contestants = $stmt->fetchAll();

    // Thống kê bình chọn theo ngày (7 ngày gần nhất)
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as vote_count
        FROM votes
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $vote_stats = $stmt->fetchAll();

    // Thống kê người dùng mới theo ngày (7 ngày gần nhất)
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as user_count
        FROM users
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $user_stats = $stmt->fetchAll();

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
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
                    <h1 class="h2">Thống kê</h1>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Thống kê tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng số cuộc thi</h5>
                                <h2 class="card-text"><?php echo number_format($stats['contests']); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng số người dùng</h5>
                                <h2 class="card-text"><?php echo number_format($stats['users']); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng số thí sinh</h5>
                                <h2 class="card-text"><?php echo number_format($stats['contestants']); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng số lượt bình chọn</h5>
                                <h2 class="card-text"><?php echo number_format($stats['votes']); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Biểu đồ thống kê -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Thống kê 7 ngày gần nhất</h5>
                                <canvas id="statsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 cuộc thi -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Top 5 cuộc thi có nhiều thí sinh</h5>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($top_contests as $contest): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($contest['title']); ?></h6>
                                                <small><?php echo number_format($contest['contestant_count']); ?> thí sinh</small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 5 thí sinh -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Top 5 thí sinh được bình chọn nhiều nhất</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Thí sinh</th>
                                        <th>Cuộc thi</th>
                                        <th>Số lượt bình chọn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_contestants as $contestant): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../uploads/<?php echo htmlspecialchars($contestant['image']); ?>"
                                                         alt="<?php echo htmlspecialchars($contestant['name']); ?>"
                                                         class="rounded-circle me-2"
                                                         width="40" height="40">
                                                    <div>
                                                        <div><?php echo htmlspecialchars($contestant['name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($contestant['user_name']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($contestant['contest_title']); ?></td>
                                            <td><?php echo number_format($contestant['vote_count']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dữ liệu cho biểu đồ
        const voteData = <?php echo json_encode(array_reverse($vote_stats)); ?>;
        const userData = <?php echo json_encode(array_reverse($user_stats)); ?>;

        // Tạo biểu đồ
        const ctx = document.getElementById('statsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: voteData.map(item => item.date),
                datasets: [
                    {
                        label: 'Lượt bình chọn',
                        data: voteData.map(item => item.vote_count),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    },
                    {
                        label: 'Người dùng mới',
                        data: userData.map(item => item.user_count),
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>