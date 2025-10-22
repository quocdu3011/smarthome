<?php
// Prevent direct access
if (!defined('INCLUDED_FROM_INDEX')) {
    die('Direct access not permitted');
}
?>
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <img src="assets/images/logo.png" alt="Smart Home">
                    <span>Smart Home</span>
                </a>
                <button class="btn btn-link d-md-none" id="closeSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-3">
                <div class="nav flex-column">
                    <a href="dashboard.php" class="nav-link<?php echo $currentPage === 'dashboard' ? ' active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                    
                    <a href="rfid.php" class="nav-link<?php echo $currentPage === 'rfid' ? ' active' : ''; ?>">
                        <i class="fas fa-id-card"></i>
                        <span>Quản lý RFID</span>
                    </a>
                    
                    <a href="sensors.php" class="nav-link<?php echo $currentPage === 'sensors' ? ' active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Thống kê cảm biến</span>
                    </a>
                    
                    <a href="devices.php" class="nav-link<?php echo $currentPage === 'devices' ? ' active' : ''; ?>">
                        <i class="fas fa-microchip"></i>
                        <span>Quản lý thiết bị</span>
                    </a>
                    
                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="users.php" class="nav-link<?php echo $currentPage === 'users' ? ' active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Quản lý tài khoản</span>
                    </a>
                    <?php endif; ?>
                    
                    <hr class="my-3">
                    
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?php echo $pageTitle; ?></h1>
                <button class="btn btn-link d-md-none" id="openSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>