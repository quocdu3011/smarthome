/* Main JavaScript file */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const sidebar = document.querySelector('.sidebar');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');

    if (openSidebarBtn) {
        openSidebarBtn.addEventListener('click', function() {
            sidebar.classList.add('show');
        });
    }

    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar.classList.contains('show') && 
            !sidebar.contains(e.target) && 
            e.target !== openSidebarBtn) {
            sidebar.classList.remove('show');
        }
    });

    // Token refresh and validation
    function checkToken() {
        const token = localStorage.getItem('token');
        if (token) {
            // Add token to all AJAX requests
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            });
        }
    }

    // Check token on page load
    checkToken();

    // Handle unauthorized responses
    $(document).ajaxError(function(event, jqXHR) {
        if (jqXHR.status === 401) {
            // Token expired or invalid
            window.location.href = 'index.php';
        }
    });

    // Show loading indicator
    $(document).on({
        ajaxStart: function() { 
            $('body').addClass('loading');
        },
        ajaxStop: function() { 
            $('body').removeClass('loading');
        }
    });

    // Initialize tooltips and popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Form validation helper
    window.validateForm = function(formElement, rules = {}) {
        let isValid = true;
        const errors = {};

        // Reset previous errors
        formElement.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        formElement.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Check required fields
        formElement.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                errors[field.name] = 'Trường này là bắt buộc';
            }
        });

        // Apply custom rules
        for (const [fieldName, rule] of Object.entries(rules)) {
            const field = formElement.querySelector(`[name="${fieldName}"]`);
            if (field && !rule.test(field.value)) {
                isValid = false;
                errors[fieldName] = rule.message;
            }
        }

        // Show errors
        for (const [fieldName, message] of Object.entries(errors)) {
            const field = formElement.querySelector(`[name="${fieldName}"]`);
            field.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            field.parentNode.appendChild(feedback);
        }

        return isValid;
    };

    // Date formatting helper
    window.formatDate = function(date, format = 'dd/MM/yyyy HH:mm') {
        if (!(date instanceof Date)) {
            date = new Date(date);
        }
        
        const pad = (n) => n.toString().padStart(2, '0');
        
        const replacements = {
            'dd': pad(date.getDate()),
            'MM': pad(date.getMonth() + 1),
            'yyyy': date.getFullYear(),
            'HH': pad(date.getHours()),
            'mm': pad(date.getMinutes()),
            'ss': pad(date.getSeconds())
        };
        
        return format.replace(/dd|MM|yyyy|HH|mm|ss/g, match => replacements[match]);
    };
});