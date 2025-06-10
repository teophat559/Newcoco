<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="<?php echo SITE_NAME; ?>" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'contests.php' ? 'active' : ''; ?>" href="contests.php">Cuộc thi</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="profile.php">Tài khoản</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle text-dark text-decoration-none" type="button" data-bs-toggle="dropdown">
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Thông tin cá nhân</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="register.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>