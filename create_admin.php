<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use SmartHome\Models\UserModel;

// Kiểm tra xem script có đang chạy từ command line không
if (php_sapi_name() !== 'cli') {
    die('Script này chỉ có thể chạy từ command line.');
}

function createAdmin() {
    try {
        $userModel = new UserModel();
        
        // Kiểm tra xem admin đã tồn tại chưa
        $existingAdmin = $userModel->getUserByUsername('admin');
        if ($existingAdmin) {
            echo "Tài khoản admin đã tồn tại!\n";
            return;
        }

        // Tạo mật khẩu mạnh
        $password = generateStrongPassword();
        
        // Tạo tài khoản admin
        $result = $userModel->createUser(
            'admin',
            $password,
            'admin@localhost',
            'admin'
        );

        if ($result) {
            echo "\n=== Tài khoản Admin đã được tạo thành công ===\n";
            echo "Username: admin\n";
            echo "Password: $password\n";
            echo "Email: admin@localhost\n";
            echo "===========================================\n";
            echo "\nVui lòng lưu thông tin đăng nhập ở nơi an toàn!\n";
        } else {
            echo "Không thể tạo tài khoản admin!\n";
        }
    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage() . "\n";
    }
}

function generateStrongPassword($length = 16) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    $password = '';
    
    // Đảm bảo có ít nhất một ký tự từ mỗi nhóm
    $password .= $chars[random_int(0, 25)]; // chữ thường
    $password .= $chars[random_int(26, 51)]; // chữ hoa
    $password .= $chars[random_int(52, 61)]; // số
    $password .= $chars[random_int(62, strlen($chars) - 1)]; // ký tự đặc biệt
    
    // Thêm các ký tự ngẫu nhiên cho đến đủ độ dài
    while (strlen($password) < $length) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    // Trộn các ký tự
    $password = str_split($password);
    shuffle($password);
    return implode('', $password);
}

// Xác nhận từ người dùng
echo "Bạn có chắc chắn muốn tạo tài khoản admin mới? [y/N]: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
if (strtolower($line) === 'y') {
    createAdmin();
} else {
    echo "Đã hủy tạo tài khoản.\n";
}
fclose($handle);
//]iUbn)0]2b1gZS+F