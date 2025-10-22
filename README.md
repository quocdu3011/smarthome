# Hệ Thống Smart Home

Hệ thống quản lý nhà thông minh toàn diện với khả năng giám sát cảm biến, quản lý thiết bị và điều khiển từ xa.

## Mục Lục

- [Giới Thiệu](#giới-thiệu)
- [Tính Năng](#tính-năng)
- [Yêu Cầu Hệ Thống](#yêu-cầu-hệ-thống)
- [Cài Đặt](#cài-đặt)
- [Cấu Hình](#cấu-hình)
- [Sử Dụng API](#sử-dụng-api)
- [Bảo Mật](#bảo-mật)
- [Khắc Phục Sự Cố](#khắc-phục-sự-cố)
- [Bảo Trì](#bảo-trì)

## Giới Thiệu

Hệ thống Smart Home cung cấp giải pháp toàn diện cho việc quản lý và giám sát ngôi nhà thông minh của bạn. Hệ thống bao gồm:

- **Giám sát cảm biến** thời gian thực
- **Quản lý người dùng** và phân quyền
- **Hệ thống RFID** cho ra vào
- **API** mạnh mẽ cho tích hợp
- **Giao diện web** responsive

## Tính Năng

### Bảo Mật & Xác Thực
- Xác thực JWT và API Key
- Quản lý người dùng đa cấp
- Hệ thống RFID an toàn
- Rate limiting và bảo vệ DDoS

### Giám Sát & Phân Tích
- Dữ liệu cảm biến thời gian thực
- Biểu đồ và thống kê trực quan
- Lịch sử dữ liệu theo thời gian
- Xuất dữ liệu CSV

### Quản Lý Thiết Bị
- Đăng ký và quản lý thiết bị
- API keys cho thiết bị IoT
- Giám sát trạng thái thiết bị

## Yêu Cầu Hệ Thống

### Máy Chủ
- **PHP**: 8.0 hoặc cao hơn
- **MySQL**: 5.7 hoặc cao hơn
- **Web Server**: Apache/Nginx
- **PHP Extensions**:
  - PDO & PDO_MySQL
  - JSON
  - OpenSSL
  - mbstring

### Client
- Trình duyệt web hiện đại hỗ trợ JavaScript
- Kết nối internet cho truy cập từ xa

## Cài Đặt

### 1. Cài Đặt Database

```sql
-- Tạo database
CREATE DATABASE smart_home;
USE smart_home;

-- Import schema
mysql -u your_user -p smart_home < smart_home.sql
mysql -u your_user -p smart_home < users.sql
mysql -u your_user -p smart_home < logs.sql
```
### 2. Cấu Hình

```bash
# Sao chép file cấu hình mẫu
cp config.sample.php config.php
```
Chỉnh sửa config.php với thông tin của bạn:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_home');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('JWT_SECRET', 'generate_strong_random_secret_here');
```
# ⚙️ 3. Thiết Lập Ban Đầu

## 🚪 Truy cập hệ thống
URL: [http://your-domain/](http://your-domain/)

### 🔑 Tạo tài khoản admin
Chạy lệnh sau trên terminal
```bash
php <Đường dẫn tới folder của bạn>\create_admin.php
```

> ⚠️ **QUAN TRỌNG:** Chỉ có thể truy cập đường dẫn trên qua CLI

---

## 🔐 Sử Dụng API

### Xác Thực
Tất cả các **API endpoints** yêu cầu xác thực bằng **JWT token** và/hoặc **API key**.

- **JWT token:** gửi trong header `Authorization`  
- **API key:** gửi trong query string  

#### Ví dụ:
```text
Authorization: Bearer <jwt_token>
```

hoặc:

```text
GET /api/get_latest.php?api_key=<your_api_key>
```

---

## 📡 Endpoints Chính

### 🔹 Dữ Liệu Cảm Biến
| Phương thức | Endpoint | Mô tả |
|--------------|-----------|-------|
| `GET` | `/api/get_latest.php` | Lấy dữ liệu mới nhất |
| `GET` | `/api/get_history.php` | Lấy lịch sử dữ liệu |
| `POST` | `/api/insert_sensor.php` | Thêm dữ liệu cảm biến |

### 👤 Quản Lý Người Dùng
| Phương thức | Endpoint | Mô tả |
|--------------|-----------|-------|
| `POST` | `/api/add_user.php` | Thêm người dùng mới |
| `POST` | `/api/check_rfid.php` | Kiểm tra RFID |
| `POST` | `/api/delete_rfid_user.php` | Xóa người dùng RFID |

### 💡 Quản Lý Thiết Bị
| Phương thức | Endpoint | Mô tả |
|--------------|-----------|-------|
| `POST` | `/api/register_device.php` | Đăng ký thiết bị mới |

---

## 🧾 Định Dạng Dữ Liệu

- **DateTime:** `YYYY-MM-DD HH:mm:ss`
- **Loại cảm biến:**
  - `temperature`
  - `humidity`
  - `motion`
  - `light`
  - `pressure`

---


```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 🔸 Cấu Hình Firewall
- Mở port `80` / `443` cho web traffic  
- Giới hạn port MySQL (`3306`) chỉ cho `localhost`  
- Cấu hình **fail2ban** cho `SSH` và `HTTP`

---

## 🧰 Khắc Phục Sự Cố

### 1️⃣ Lỗi Kết Nối Database
- Kiểm tra thông tin trong `config.php`
- Xác nhận **MySQL service** đang chạy
- Kiểm tra quyền truy cập của user MySQL

### 2️⃣ Lỗi API
- Xác minh định dạng `api_key`
- Kiểm tra **server logs** để biết chi tiết lỗi
- Đảm bảo định dạng **JSON request** đúng

---

## 💬 Liên Hệ Hỗ Trợ

- **Email:** [quocdu3011@gmail.com](mailto:quocdu3011@gmail.com)  
- **Tài liệu:** `/docs/`  
- **Theo dõi lỗi:** [github.com/quocdu3011/smarthome/issues](https://github.com/quocdu3011/smarthome/issues)

---

## 🧩 Bảo Trì

### 🕒 Công Việc Định Kỳ
- Backup database tự động hàng ngày  
- Xoay vòng log hàng tuần  
- Kiểm tra system logs để phát hiện lỗi  
- Cập nhật **PHP** và **MySQL** định kỳ  

### 💾 Chiến Lược Backup
- Backup tự động hàng ngày  
- Giữ lại **30 bản backup gần nhất**  
- Kiểm tra **quy trình khôi phục (restore)** hàng tháng  


## 🤝 Đóng Góp

Đóng góp luôn được chào đón!  
