/* RFID Management JavaScript */

let deleteId = null;
let dateRangePicker = null;

document.addEventListener('DOMContentLoaded', function() {
    // Configure flatpickr
    flatpickr.localize(flatpickr.l10ns.vn);

    // Initialize datepicker for start date
    const startDatePicker = flatpickr("#startDate", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        defaultDate: moment().subtract(24, 'hours').toDate(),
        onChange: function(selectedDates) {
            endDatePicker.set('minDate', selectedDates[0]);
            loadRFIDLogs();
        }
    });

    // Initialize datepicker for end date
    const endDatePicker = flatpickr("#endDate", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        defaultDate: new Date(),
        onChange: function(selectedDates) {
            startDatePicker.set('maxDate', selectedDates[0]);
            loadRFIDLogs();
        }
    });

    // Event listeners
    document.getElementById('refreshLogs').addEventListener('click', loadRFIDLogs);
    document.getElementById('confirmDelete').addEventListener('click', confirmDelete);
    document.getElementById('editRFIDForm').addEventListener('submit', handleEdit);
    
    // Initial load
    loadRFIDUsers();
    loadRFIDLogs();
});

function loadRFIDUsers() {
    fetch('partials/process_rfid.php?action=list')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('rfidUsersTable');
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">Chưa có thẻ RFID nào</td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = data.map(user => `
                <tr>
                    <td><code>${user.uid}</code></td>
                    <td>${escapeHtml(user.name)}</td>
                    <td>
                        <small class="text-muted">
                            ${user.meta ? JSON.stringify(JSON.parse(user.meta), null, 2) : ''}
                        </small>
                    </td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" 
                                onclick="showEditModal(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="showDeleteModal(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading RFID users:', error);
            showToast('error', 'Không thể tải danh sách thẻ RFID');
        });
}

function loadRFIDLogs() {
    const startDate = document.getElementById('startDate')._flatpickr.selectedDates[0];
    const endDate = document.getElementById('endDate')._flatpickr.selectedDates[0];
    
    const dates = startDate && endDate ? {
        start: moment(startDate).format('YYYY-MM-DD HH:mm:ss'),
        end: moment(endDate).format('YYYY-MM-DD HH:mm:ss')
    } : {};

    fetch('partials/process_rfid.php?' + new URLSearchParams({
        action: 'logs',
        ...dates
    }))
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('rfidLogsTable');
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">Chưa có lịch sử quét thẻ</td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = data.map(log => `
                <tr>
                    <td>${formatDate(log.logged_at)}</td>
                    <td><code>${log.uid}</code></td>
                    <td>${log.user_id ? escapeHtml(log.name) : 'Không xác định'}</td>
                    <td>
                        ${log.user_id ? 
                            '<span class="badge bg-success">Hợp lệ</span>' : 
                            '<span class="badge bg-danger">Không hợp lệ</span>'}
                    </td>
                    <td><small class="text-muted">${escapeHtml(log.note || '')}</small></td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading RFID logs:', error);
            showToast('error', 'Không thể tải lịch sử quét thẻ');
        });
}

function showEditModal(id) {
    fetch(`partials/process_rfid.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editId').value = data.id;
            document.getElementById('editUid').value = data.uid;
            document.getElementById('editName').value = data.name;
            document.getElementById('editMeta').value = data.meta ? 
                JSON.stringify(JSON.parse(data.meta), null, 2) : '';
            
            new bootstrap.Modal(document.getElementById('editRFIDModal')).show();
        })
        .catch(error => {
            console.error('Error loading RFID user:', error);
            showToast('error', 'Không thể tải thông tin thẻ RFID');
        });
}

function showDeleteModal(id) {
    fetch(`partials/process_rfid.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            deleteId = data.id;
            document.getElementById('deleteUid').textContent = data.uid;
            document.getElementById('deleteName').textContent = data.name;
            
            new bootstrap.Modal(document.getElementById('deleteRFIDModal')).show();
        })
        .catch(error => {
            console.error('Error loading RFID user:', error);
            showToast('error', 'Không thể tải thông tin thẻ RFID');
        });
}

async function handleEdit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validate meta JSON if provided
    if (data.meta) {
        try {
            JSON.parse(data.meta);
        } catch (e) {
            showToast('error', 'Thông tin thêm phải là định dạng JSON hợp lệ');
            return;
        }
    }

    try {
        const response = await fetch('partials/process_rfid.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editRFIDModal')).hide();
            showToast('success', 'Cập nhật thành công');
            loadRFIDUsers();
        } else {
            showToast('error', result.message || 'Cập nhật thất bại');
        }
    } catch (error) {
        console.error('Error updating RFID user:', error);
        showToast('error', 'Không thể cập nhật thông tin thẻ RFID');
    }
}

async function confirmDelete() {
    if (!deleteId) return;

    try {
        const response = await fetch('partials/process_rfid.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: deleteId })
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteRFIDModal')).hide();
            showToast('success', 'Xóa thành công');
            loadRFIDUsers();
        } else {
            showToast('error', result.message || 'Xóa thất bại');
        }
    } catch (error) {
        console.error('Error deleting RFID user:', error);
        showToast('error', 'Không thể xóa thẻ RFID');
    }
}

function syncRFIDUsers() {
    showToast('info', 'Đang đồng bộ...', 2000);
    loadRFIDUsers();
}

function escapeHtml(unsafe) {
    return unsafe
        ? unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
        : '';
}

function showToast(type, message, duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: duration });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}