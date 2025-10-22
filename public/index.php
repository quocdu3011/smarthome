<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="Smart Home Logo" class="mb-4" style="max-width: 150px;">
                            <h2 class="fw-bold">Smart Home</h2>
                            <p class="text-muted">Đăng nhập để quản lý hệ thống</p>
                        </div>
                        
                        <form id="loginForm">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" required>
                                <label for="username">Tên đăng nhập</label>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                                <label for="password">Mật khẩu</label>
                            </div>

                            <div class="alert alert-danger d-none" id="loginError"></div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Đăng nhập
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>