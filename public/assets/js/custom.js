// Custom JavaScript Functions

// Show notification
function showNotification(type, message, duration = 3000) {
    const container = document.getElementById('notification-container') || createNotificationContainer();
    
    const notification = document.createElement('div');
    notification.className = `notification ${type} fade-enter`;
    
    const icon = document.createElement('i');
    icon.className = getNotificationIcon(type);
    notification.appendChild(icon);
    
    const text = document.createElement('span');
    text.textContent = message;
    notification.appendChild(text);
    
    const closeButton = document.createElement('button');
    closeButton.innerHTML = '&times;';
    closeButton.onclick = () => removeNotification(notification);
    notification.appendChild(closeButton);
    
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('fade-enter-active'), 10);
    
    // Auto remove
    setTimeout(() => removeNotification(notification), duration);
}

// Create notification container
function createNotificationContainer() {
    const container = document.createElement('div');
    container.id = 'notification-container';
    container.className = 'fixed top-4 right-4 z-50 space-y-2';
    document.body.appendChild(container);
    return container;
}

// Remove notification
function removeNotification(notification) {
    notification.classList.remove('fade-enter-active');
    notification.classList.add('fade-exit-active');
    setTimeout(() => notification.remove(), 200);
}

// Get notification icon
function getNotificationIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Format number
function formatNumber(number) {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(1) + 'M';
    }
    if (number >= 1000) {
        return (number / 1000).toFixed(1) + 'K';
    }
    return number.toString();
}

// Format date
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day)
        .replace('HH', hours)
        .replace('mm', minutes);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Copy to clipboard
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

// Validate form
function validateForm(form) {
    const errors = {};
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        const value = input.value.trim();
        
        // Required fields
        if (input.hasAttribute('required') && !value) {
            errors[input.name] = 'This field is required';
            return;
        }
        
        // Email validation
        if (input.type === 'email' && value && !isValidEmail(value)) {
            errors[input.name] = 'Invalid email address';
            return;
        }
        
        // Minimum length
        const minLength = input.getAttribute('minlength');
        if (minLength && value.length < parseInt(minLength)) {
            errors[input.name] = `Must be at least ${minLength} characters`;
            return;
        }
        
        // Maximum length
        const maxLength = input.getAttribute('maxlength');
        if (maxLength && value.length > parseInt(maxLength)) {
            errors[input.name] = `Must not exceed ${maxLength} characters`;
            return;
        }
        
        // Pattern matching
        const pattern = input.getAttribute('pattern');
        if (pattern && value && !new RegExp(pattern).test(value)) {
            errors[input.name] = 'Invalid format';
            return;
        }
    });
    
    return errors;
}

// Validate email
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Handle file upload
function handleFileUpload(input, options = {}) {
    const file = input.files[0];
    if (!file) return;
    
    // Check file size
    if (options.maxSize && file.size > options.maxSize) {
        showNotification('error', 'File size exceeds limit');
        input.value = '';
        return;
    }
    
    // Check file type
    if (options.allowedTypes && !options.allowedTypes.includes(file.type)) {
        showNotification('error', 'File type not allowed');
        input.value = '';
        return;
    }
    
    // Preview image
    if (options.previewElement && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            options.previewElement.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    // Custom callback
    if (options.onUpload) {
        options.onUpload(file);
    }
}

// Handle infinite scroll
function handleInfiniteScroll(container, callback, options = {}) {
    const threshold = options.threshold || 100;
    let loading = false;
    
    container.addEventListener('scroll', debounce(() => {
        if (loading) return;
        
        const scrollHeight = container.scrollHeight;
        const scrollTop = container.scrollTop;
        const clientHeight = container.clientHeight;
        
        if (scrollHeight - scrollTop - clientHeight < threshold) {
            loading = true;
            callback().finally(() => {
                loading = false;
            });
        }
    }, 100));
}

// Initialize tooltips
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-text';
        tooltip.textContent = element.getAttribute('data-tooltip');
        element.appendChild(tooltip);
    });
}

// Initialize modals
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const modalId = trigger.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('modal-enter');
                setTimeout(() => modal.classList.add('modal-enter-active'), 10);
            }
        });
    });
    
    const modalCloseButtons = document.querySelectorAll('[data-modal-close]');
    modalCloseButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal');
            if (modal) {
                modal.classList.remove('modal-enter-active');
                modal.classList.add('modal-exit-active');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('modal-exit-active');
                }, 300);
            }
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initTooltips();
    initModals();
});

// Export functions
window.showNotification = showNotification;
window.formatNumber = formatNumber;
window.formatDate = formatDate;
window.copyToClipboard = copyToClipboard;
window.validateForm = validateForm;
window.handleFileUpload = handleFileUpload;
