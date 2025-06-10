<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar bg-dark text-white">
    <div class="p-3">
        <a href="<?php echo APP_URL; ?>/admin" class="d-flex align-items-center text-white text-decoration-none">
            <img src="<?php echo APP_URL; ?>/assets/img/logo.png" alt="Logo" class="me-2" style="width: 40px;">
            <span class="fs-4"><?php echo APP_NAME; ?></span>
        </a>
    </div>

    <!-- Mini Stat Box -->
    <div class="mini-stat-box my-3 mx-3">
      <div class="mini-stat-title">
        📊 Bảng Thống Kê Mini
      </div>
      <div class="mini-stat-list">
        <div class="mini-stat-row"><span>Online</span> <span class="stat-green">12</span></div>
        <div class="mini-stat-row"><span>Thành Công</span> <span class="stat-green">98</span></div>
        <div class="mini-stat-row"><span>Lượt Truy Cập</span> <span class="stat-green">1234</span></div>
        <div class="mini-stat-row"><span>Lượt Thất Bại</span> <span class="stat-red">5</span></div>
      </div>
    </div>

    <hr class="text-white-50">

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin" class="nav-link text-white <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home me-2"></i>
                Trang chủ
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/contests.php" class="nav-link text-white <?php echo $current_page == 'contests.php' ? 'active' : ''; ?>">
                <i class="fas fa-trophy me-2"></i>
                Cuộc thi
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/contestants.php" class="nav-link text-white <?php echo $current_page == 'contestants.php' ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i>
                Thí sinh
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/votes.php" class="nav-link text-white <?php echo $current_page == 'votes.php' ? 'active' : ''; ?>">
                <i class="fas fa-vote-yea me-2"></i>
                Bình chọn
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/users.php" class="nav-link text-white <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-user me-2"></i>
                Người dùng
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/admins.php" class="nav-link text-white <?php echo $current_page == 'admins.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield me-2"></i>
                Quản trị viên
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/statistics.php" class="nav-link text-white <?php echo $current_page == 'statistics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-2"></i>
                Thống kê
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/logs.php" class="nav-link text-white <?php echo $current_page == 'logs.php' ? 'active' : ''; ?>">
                <i class="fas fa-history me-2"></i>
                Nhật ký
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/backup.php" class="nav-link text-white <?php echo $current_page == 'backup.php' ? 'active' : ''; ?>">
                <i class="fas fa-database me-2"></i>
                Sao lưu
            </a>
        </li>

        <li class="nav-item">
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="nav-link text-white <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog me-2"></i>
                Cài đặt
            </a>
        </li>
    </ul>

    <hr class="text-white-50">

    <div class="dropdown p-3">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo APP_URL; ?>/assets/img/avatars/<?php echo $_SESSION['admin_avatar'] ?? 'default.png'; ?>" alt="Avatar" width="32" height="32" class="rounded-circle me-2">
            <strong><?php echo $_SESSION['admin_name']; ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/profile.php">Hồ sơ</a></li>
            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/settings.php">Cài đặt</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/logout.php">Đăng xuất</a></li>
        </ul>
    </div>
</div>

<script>
// Toggle sidebar on mobile
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        });
    }
});
</script>

<style>
.mini-stat-box {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 18px 20px;
  width: 220px;
  min-height: 170px !important;
  transition: min-height 0.4s cubic-bezier(0.4,0,0.2,1), font-size 0.4s cubic-bezier(0.4,0,0.2,1), box-shadow 0.4s;
  font-size: 1.05rem !important;
  overflow: hidden;
  color: #222;
  cursor: pointer;
}
.mini-stat-box:hover {
  min-height: 240px !important;
  font-size: 1.22rem !important;
  box-shadow: 0 6px 24px rgba(0,0,0,0.10);
}
.mini-stat-title {
  font-weight: bold;
  margin-bottom: 10px;
  color: #222;
}
.mini-stat-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.mini-stat-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.stat-green {
  color: #1abc9c;
  font-weight: bold;
}
.stat-red {
  color: #e74c3c;
  font-weight: bold;
}
</style>