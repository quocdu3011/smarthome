<?php
define('INCLUDED_FROM_INDEX', true);
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Quản lý thiết bị';
$currentPage = 'devices';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Devices Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách thiết bị</h5>
            <button type="button" class="btn btn-primary" onclick="showAddModal()">
                <i class="fas fa-plus me-1"></i>
                Thêm thiết bị mới
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên thiết bị</th>
                        <th>API Key</th>
                        <th>Trạng thái</th>
                        <th>Dữ liệu cuối</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="devicesTable">
                    <tr>
                        <td colspan="7" class="text-center">Đang tải...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm thiết bị mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDeviceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="deviceName" class="form-label">Tên thiết bị</label>
                        <input type="text" class="form-control" id="deviceName" name="device_name" required>
                        <div class="form-text">Đặt tên dễ nhớ để phân biệt các thiết bị</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Tạo thiết bị</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeviceForm">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="mb-3">
                        <label for="editName" class="form-label">Tên thiết bị</label>
                        <input type="text" class="form-control" id="editName" name="device_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="editApiKey" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-outline-warning" type="button" onclick="regenerateApiKey()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div class="form-text text-danger">
                            Cảnh báo: Tạo API key mới sẽ vô hiệu hóa key cũ
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thống kê</label>
                        <div class="row text-center">
                            <div class="col">
                                <h4 id="deviceDataCount">-</h4>
                                <small class="text-muted">Số bản ghi</small>
                            </div>
                            <div class="col">
                                <h4 id="deviceLastData">-</h4>
                                <small class="text-muted">Lần cuối</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto" onclick="showDeleteModal()">
                        <i class="fas fa-trash me-1"></i>
                        Xóa thiết bị
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Device Modal -->
<div class="modal fade" id="deleteDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa thiết bị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cảnh báo: Thao tác này không thể hoàn tác!
                </div>
                <p>Bạn có chắc chắn muốn xóa thiết bị này?</p>
                <p class="mb-0"><strong>Tên thiết bị:</strong> <span id="deleteName"></span></p>
                <p class="mb-0"><strong>ID:</strong> <span id="deleteId"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<?php
$customJs = 'assets/js/devices.js';
include __DIR__ . '/../includes/footer.php';
?>