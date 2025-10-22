/* Authentication utilities */

// Get token from meta tag or localStorage
function getToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || localStorage.getItem('token');
}

// Setup AJAX token handling
function setupAjaxToken() {
    const token = getToken();
    if (!token) return;

    // Add token to all fetch requests
    const originalFetch = window.fetch;
    window.fetch = function() {
        let [resource, config] = arguments;
        
        if(typeof resource === 'string') {
            resource = new Request(resource);
        }
        
        const modifiedRequest = new Request(resource, {
            ...config,
            headers: {
                ...resource.headers,
                ...(config?.headers || {}),
                'Authorization': `Bearer ${token}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        return originalFetch.apply(this, [modifiedRequest]);
    };

    // Add token to jQuery AJAX requests if jQuery exists
    if (window.jQuery) {
        jQuery.ajaxSetup({
            headers: {
                'Authorization': `Bearer ${token}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupAjaxToken);
} else {
    setupAjaxToken();
}

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) {
        setupAjaxToken();
        return;
    }
    const loginError = document.getElementById('loginError');

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        loginError.classList.add('d-none');
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch('partials/process_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Store token in localStorage
                localStorage.setItem('token', data.token);
                // Redirect to dashboard
                window.location.href = 'dashboard.php';
            } else {
                loginError.textContent = data.message || 'Đăng nhập thất bại';
                loginError.classList.remove('d-none');
            }
        } catch (error) {
            loginError.textContent = 'Có lỗi xảy ra, vui lòng thử lại';
            loginError.classList.remove('d-none');
        }
    });
});