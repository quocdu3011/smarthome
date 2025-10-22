<?php
define('INCLUDED_FROM_INDEX', true);
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Quản lý thẻ RFID';
$currentPage = 'rfid';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- RFID Users Table -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách thẻ RFID</h5>
            <button type="button" class="btn btn-primary" onclick="syncRFIDUsers()">
                <i class="fas fa-sync-alt"></i> Đồng bộ
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Tên</th>
                        <th>Thông tin thêm</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="rfidUsersTable">
                    <tr>
                        <td colspan="5" class="text-center">Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- RFID Logs -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lịch sử quét thẻ</h5>
            <div>
                <div class="input-group">
                    <input type="text" class="form-control" id="startDate" placeholder="Thời gian bắt đầu">
                    <input type="text" class="form-control" id="endDate" placeholder="Thời gian kết thúc">
                    <button class="btn btn-outline-secondary" type="button" id="refreshLogs">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>UID</th>
                        <th>Người dùng</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody id="rfidLogsTable">
                    <tr>
                        <td colspan="5" class="text-center">Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit RFID User Modal -->
<div class="modal fade" id="editRFIDModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa thông tin thẻ RFID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRFIDForm">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="mb-3">
                        <label for="editUid" class="form-label">UID</label>
                        <input type="text" class="form-control" id="editUid" name="uid" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editName" class="form-label">Tên</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editMeta" class="form-label">Thông tin thêm (JSON)</label>
                        <textarea class="form-control" id="editMeta" name="meta" rows="3"></textarea>
                        <div class="form-text">Định dạng JSON hợp lệ, ví dụ: {"role": "admin", "notes": "Ghi chú"}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRFIDModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa thẻ RFID này?</p>
                <p class="mb-0"><strong>UID:</strong> <span id="deleteUid"></span></p>
                <p class="mb-0"><strong>Tên:</strong> <span id="deleteName"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<?php
$customCss = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css';
$customJs = 'assets/js/rfid.js';
include __DIR__ . '/../includes/footer.php';
?>