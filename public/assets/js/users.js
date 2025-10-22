/* User Management JavaScript */

let currentUser = null;

document.addEventListener('DOMContentLoaded', function() {
    // Event listeners
    document.getElementById('addUserForm').addEventListener('submit', handleAdd);
    document.getElementById('editUserForm').addEventListener('submit', handleEdit);
    document.getElementById('confirmDelete').addEventListener('click', confirmDelete);
    
    // Initial load
    loadUsers();
});

function loadUsers() {
    fetch('partials/process_users.php?action=list')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('usersTable');
            if (!data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center">Chưa có tài khoản nào</td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = data.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${escapeHtml(user.username)}</td>
                    <td>${escapeHtml(user.email)}</td>
                    <td>
                        <span class="badge bg-${user.role === 'admin' ? 'danger' : 'primary'}">
                            ${user.role === 'admin' ? 'Admin' : 'User'}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">
                            ${user.status === 'active' ? 'Hoạt động' : 'Vô hiệu'}
                        </span>
                    </td>
                    <td>${user.last_login ? formatDate(user.last_login) : 'Chưa đăng nhập'}</td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="showEditModal(${user.id})"
                                ${user.username === 'admin' ? 'disabled' : ''}>
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showToast('error', 'Không thể tải danh sách tài khoản');
        });
}

function showAddModal() {
    document.getElementById('addUserForm').reset();
    new bootstrap.Modal(document.getElementById('addUserModal')).show();
}

function showEditModal(id) {
    fetch(`partials/process_users.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(user => {
            currentUser = user;
            
            document.getElementById('editId').value = user.id;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editRole').value = user.role;
            document.getElementById('editStatus').value = user.status;
            document.getElementById('editPassword').value = '';
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        })
        .catch(error => {
            console.error('Error loading user:', error);
            showToast('error', 'Không thể tải thông tin tài khoản');
        });
}

function showDeleteModal() {
    if (!currentUser) return;
    
    // Prevent deleting admin user
    if (currentUser.username === 'admin') {
        showToast('error', 'Không thể xóa tài khoản admin');
        return;
    }
    
    document.getElementById('deleteUsername').textContent = currentUser.username;
    document.getElementById('deleteEmail').textContent = currentUser.email;
    
    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}

async function handleAdd(e) {
    e.preventDefault();
    
    const form = e.target;
    if (!validatePassword(form.password.value)) {
        showToast('error', 'Mật khẩu không đạt yêu cầu');
        return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('partials/process_users.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            showToast('success', 'Thêm tài khoản thành công');
            loadUsers();
        } else {
            showToast('error', result.message || 'Thêm tài khoản thất bại');
        }
    } catch (error) {
        console.error('Error adding user:', error);
        showToast('error', 'Không thể thêm tài khoản');
    }
}

async function handleEdit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Validate password if provided
    if (data.password && !validatePassword(data.password)) {
        showToast('error', 'Mật khẩu không đạt yêu cầu');
        return;
    }

    // Remove empty password
    if (!data.password) {
        delete data.password;
    }

    try {
        const response = await fetch('partials/process_users.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            showToast('success', 'Cập nhật thành công');
            loadUsers();
        } else {
            showToast('error', result.message || 'Cập nhật thất bại');
        }
    } catch (error) {
        console.error('Error updating user:', error);
        showToast('error', 'Không thể cập nhật tài khoản');
    }
}

async function confirmDelete() {
    if (!currentUser) return;

    try {
        const response = await fetch('partials/process_users.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: currentUser.id })
        });

        const result = await response.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
            showToast('success', 'Xóa tài khoản thành công');
            loadUsers();
        } else {
            showToast('error', result.message || 'Xóa tài khoản thất bại');
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showToast('error', 'Không thể xóa tài khoản');
    }
}

function validatePassword(password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
    return regex.test(password);
}