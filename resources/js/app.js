import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Global utility functions
window.utils = {
    // Format currency
    formatCurrency: function(amount, currency = 'so\'m') {
        return new Intl.NumberFormat('uz-UZ').format(amount) + ' ' + currency;
    },

    // Format date
    formatDate: function(date) {
        return new Date(date).toLocaleDateString('uz-UZ', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    // Show notification
    showNotification: function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        
        notification.className += ` ${colors[type]}`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info'}-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    },

    // Confirm action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
};

// AJAX utility
window.ajax = {
    // GET request
    get: function(url, options = {}) {
        return fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            },
            ...options
        }).then(response => response.json());
    },

    // POST request
    post: function(url, data = {}, options = {}) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        }).then(response => response.json());
    },

    // PUT request
    put: function(url, data = {}, options = {}) {
        return fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        }).then(response => response.json());
    },

    // DELETE request
    delete: function(url, options = {}) {
        return fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            },
            ...options
        }).then(response => response.json());
    }
};

// Form handling
window.formHandler = {
    // Submit form via AJAX
    submit: function(formElement, options = {}) {
        const form = typeof formElement === 'string' ? document.querySelector(formElement) : formElement;
        const formData = new FormData(form);
        
        const url = form.action;
        const method = form.method.toUpperCase();
        
        return fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            }
        }).then(response => response.json());
    },

    // Validate form
    validate: function(formElement) {
        const form = typeof formElement === 'string' ? document.querySelector(formElement) : formElement;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    },

    // Reset form
    reset: function(formElement) {
        const form = typeof formElement === 'string' ? document.querySelector(formElement) : formElement;
        form.reset();
        
        // Remove error classes
        form.querySelectorAll('.border-red-500').forEach(input => {
            input.classList.remove('border-red-500');
        });
    }
};

// Table utilities
window.tableUtils = {
    // Sort table
    sort: function(tableElement, columnIndex, type = 'string') {
        const table = typeof tableElement === 'string' ? document.querySelector(tableElement) : tableElement;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.trim();
            const bValue = b.cells[columnIndex].textContent.trim();
            
            if (type === 'number') {
                return parseFloat(aValue) - parseFloat(bValue);
            } else if (type === 'date') {
                return new Date(aValue) - new Date(bValue);
            } else {
                return aValue.localeCompare(bValue);
            }
        });
        
        rows.forEach(row => tbody.appendChild(row));
    },

    // Filter table
    filter: function(tableElement, searchTerm, columnIndex = null) {
        const table = typeof tableElement === 'string' ? document.querySelector(tableElement) : tableElement;
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let shouldShow = false;
            
            if (columnIndex !== null) {
                const cellText = cells[columnIndex].textContent.toLowerCase();
                shouldShow = cellText.includes(searchTerm.toLowerCase());
            } else {
                shouldShow = Array.from(cells).some(cell => 
                    cell.textContent.toLowerCase().includes(searchTerm.toLowerCase())
                );
            }
            
            row.style.display = shouldShow ? '' : 'none';
        });
    }
};

// Modal utilities
window.modalUtils = {
    // Show modal
    show: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    },

    // Hide modal
    hide: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    },

    // Toggle modal
    toggle: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
            document.body.style.overflow = modal.classList.contains('hidden') ? '' : 'hidden';
        }
    }
};

// Chart utilities (if Chart.js is available)
window.chartUtils = {
    // Create line chart
    createLineChart: function(canvasId, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        
        const ctx = canvas.getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                ...options
            }
        });
    },

    // Create bar chart
    createBarChart: function(canvasId, data, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        
        const ctx = canvas.getContext('2d');
        return new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                ...options
            }
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded shadow-lg';
            tooltip.textContent = this.getAttribute('data-tooltip');
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) + 'px';
            tooltip.style.top = (rect.top - 30) + 'px';
            
            document.body.appendChild(tooltip);
            this._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });

    // Initialize form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!formHandler.validate(this)) {
                e.preventDefault();
                utils.showNotification('Iltimos, barcha majburiy maydonlarni to\'ldiring', 'error');
            }
        });
    });

    // Initialize search functionality
    const searchInputs = document.querySelectorAll('[data-search]');
    searchInputs.forEach(input => {
        const debouncedSearch = utils.debounce(function() {
            const searchTerm = this.value;
            const targetTable = this.getAttribute('data-search');
            tableUtils.filter(targetTable, searchTerm);
        }, 300);
        
        input.addEventListener('input', debouncedSearch);
    });

    // Initialize sortable tables
    const sortableTables = document.querySelectorAll('[data-sortable]');
    sortableTables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach((header, index) => {
            header.addEventListener('click', function() {
                const sortType = this.getAttribute('data-sort-type') || 'string';
                tableUtils.sort(table, index, sortType);
            });
        });
    });
});

// Export for use in other modules
export { utils, ajax, formHandler, tableUtils, modalUtils, chartUtils };
