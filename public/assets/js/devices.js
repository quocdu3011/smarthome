/* Devices Management JavaScript */

let currentDevice = null;

document.addEventListener('DOMContentLoaded', function() {
    // Event listeners
    document.getElementById('addDeviceForm').addEventListener('submit', handleAdd);
    document.getElementById('editDeviceForm').addEventListener('submit', handleEdit);
    document.getElementById('confirmDelete').addEventListener('click', confirmDelete);
    
    // Initial load
    loadDevices();

    // Refresh data periodically
    setInterval(loadDevices, 30000); // every 30 seconds
});

function loadDevices() {
    fetch('partials/process_devices.php?action=list')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('devicesTable');
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center">Chưa có thiết bị nào</td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = data.map(device => `
                <tr>
                    <td>${device.id}</td>
                    <td>${escapeHtml(device.device_name)}</td>
                    <td>
                        <code class="user-select-all">${device.api_key || '<em>Đã vô hiệu</em>'}</code>
                    </td>
                    <td>
                        ${getDeviceStatus(device)}
                    </td>
                    <td>
                        ${device.last_data ? formatDate(device.last_data) : 'Chưa có'}
                    </td>
                    <td>${formatDate(device.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="showEditModal(${device.id})">
                            <i class="fas fa-cog"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading devices:', error);
            showToast('error', 'Không thể tải danh sách thiết bị');
        });
}

function getDeviceStatus(device) {
    if (!device.api_key) {
        return '<span class="badge bg-danger">Vô hiệu</span>';
    }
    if (!device.last_data) {
        return '<span class="badge bg-warning">Chưa hoạt động</span>';
    }
    
    const lastDataTime = new Date(device.last_data);
    const now = new Date();
    const diffMinutes = Math.floor((now - lastDataTime) / 1000 / 60);
    
    if (diffMinutes < 5) {
        return '<span class="badge bg-success">Hoạt động</span>';
    } else if (diffMinutes < 60) {
        return '<span class="badge bg-warning">Không hoạt động</span>';
    } else {
        return '<span class="badge bg-secondary">Ngưng hoạt động</span>';
    }
}

function showAddModal() {
    document.getElementById('addDeviceForm').reset();
    new bootstrap.Modal(document.getElementById('addDeviceModal')).show();
}

function showEditModal(id) {
    fetch(`partials/process_devices.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(device => {
            currentDevice = device;
            
            document.getElementById('editId').value = device.id;
            document.getElementById('editName').value = device.device_name;
            document.getElementById('editApiKey').value = device.api_key || '';
            document.getElementById('deviceDataCount').textContent = device.data_count || '0';
            document.getElementById('deviceLastData').textContent = device.last_data ? 
                formatDate(device.last_data) : 'Chưa có';
            
            new bootstrap.Modal(document.getElementById('editDeviceModal')).show();
        })
        .catch(error => {
            console.error('Error loading device:', error);
            showToast('error', 'Không thể tải thông tin thiết bị');
        });
}

function showDeleteModal() {
    if (!currentDevice) return;
    
    document.getElementById('deleteId').textContent = currentDevice.id;
    document.getElementById('deleteName').textContent = currentDevice.device_name;
    
    bootstrap.Modal.getInstance(document.getElementById('editDeviceModal')).hide();
    new bootstrap.Modal(document.getElementById('deleteDeviceModal')).show();
}

async function handleAdd(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('partials/process_devices.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
            showToast('success', 'Thêm thiết bị thành công');
            
            // Show API key
            await Swal.fire({
                title: 'API Key của thiết bị mới',
                html: `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Hãy lưu lại API key này, bạn sẽ không thể xem lại nó sau này!
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="${result.api_key}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('${result.api_key}')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Đã lưu'
            });
            
            loadDevices();
        } else {
            showToast('error', result.message || 'Thêm thiết bị thất bại');
        }
    } catch (error) {
        console.error('Error adding device:', error);
        showToast('error', 'Không thể thêm thiết bị');
    }
}

async function handleEdit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('partials/process_devices.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editDeviceModal')).hide();
            showToast('success', 'Cập nhật thành công');
            loadDevices();
        } else {
            showToast('error', result.message || 'Cập nhật thất bại');
        }
    } catch (error) {
        console.error('Error updating device:', error);
        showToast('error', 'Không thể cập nhật thiết bị');
    }
}

async function confirmDelete() {
    if (!currentDevice) return;

    try {
        const response = await fetch('partials/process_devices.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: currentDevice.id })
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteDeviceModal')).hide();
            showToast('success', 'Xóa thành công');
            loadDevices();
        } else {
            showToast('error', result.message || 'Xóa thất bại');
        }
    } catch (error) {
        console.error('Error deleting device:', error);
        showToast('error', 'Không thể xóa thiết bị');
    }
}

async function regenerateApiKey() {
    if (!currentDevice) return;

    const result = await Swal.fire({
        title: 'Xác nhận tạo API key mới?',
        text: 'API key cũ sẽ bị vô hiệu hóa ngay lập tức!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Tạo mới',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#dc3545'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch('partials/process_devices.php?action=regenerate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: currentDevice.id })
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById('editApiKey').value = data.api_key;
            showToast('success', 'Đã tạo API key mới');
            loadDevices();
        } else {
            showToast('error', data.message || 'Không thể tạo API key mới');
        }
    } catch (error) {
        console.error('Error regenerating API key:', error);
        showToast('error', 'Không thể tạo API key mới');
    }
}

function copyApiKey() {
    const apiKey = document.getElementById('editApiKey').value;
    if (!apiKey) return;
    
    navigator.clipboard.writeText(apiKey)
        .then(() => showToast('success', 'Đã sao chép API key'))
        .catch(() => showToast('error', 'Không thể sao chép API key'));
}