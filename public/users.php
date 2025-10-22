<?php
define('INCLUDED_FROM_INDEX', true);
require_once __DIR__ . '/../includes/auth_check.php';

// Check if user is admin
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Quản lý tài khoản';
$currentPage = 'users';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách tài khoản</h5>
            <button type="button" class="btn btn-primary" onclick="showAddModal()">
                <i class="fas fa-user-plus me-1"></i>
                Thêm tài khoản mới
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Đăng nhập cuối</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <tr>
                        <td colspan="8" class="text-center">Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm tài khoản mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">
                            Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="editUsername" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <div class="form-text">
                            Để trống nếu không muốn đổi mật khẩu
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Vai trò</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Vô hiệu hóa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto" onclick="showDeleteModal()">
                        <i class="fas fa-user-times me-1"></i>
                        Xóa tài khoản
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cảnh báo: Thao tác này không thể hoàn tác!
                </div>
                <p>Bạn có chắc chắn muốn xóa tài khoản này?</p>
                <p class="mb-0"><strong>Tên đăng nhập:</strong> <span id="deleteUsername"></span></p>
                <p class="mb-0"><strong>Email:</strong> <span id="deleteEmail"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<?php
$customJs = 'assets/js/users.js';
include __DIR__ . '/../includes/footer.php';
?>