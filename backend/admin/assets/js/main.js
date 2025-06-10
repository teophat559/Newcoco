// Admin Panel JavaScript

// Utility Functions
const showAlert = (message, type = 'success') => {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const container = document.querySelector('.content-body');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => alertDiv.remove(), 5000);
};

const confirmAction = (message) => {
    return confirm(message);
};

// AJAX Helper
const fetchData = async (url, options = {}) => {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'An error occurred');
        }

        return data;
    } catch (error) {
        console.error('Error:', error);
        showAlert(error.message, 'danger');
        throw error;
    }
};

// Form Handling
const handleFormSubmit = async (formId, url, successMessage) => {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetchData(url, {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response.success) {
                showAlert(successMessage);
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            }
        } catch (error) {
            // Error is already handled by fetchData
        }
    });
};

// Delete Handling
const handleDelete = async (id, url, confirmMessage) => {
    if (!confirmAction(confirmMessage)) return;

    try {
        const response = await fetchData(`${url}?id=${id}`, {
            method: 'POST'
        });

        if (response.success) {
            showAlert('Item deleted successfully');
            location.reload();
        }
    } catch (error) {
        // Error is already handled by fetchData
    }
};

// Table Sorting
const sortTable = (tableId, columnIndex) => {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();

        return aValue.localeCompare(bValue);
    });

    rows.forEach(row => tbody.appendChild(row));
};

// File Upload Preview
const handleFileUpload = (inputId, previewId) => {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
};

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Add active class to current menu item
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.nav-menu a');

    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
        }
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.title = tooltip.dataset.tooltip;
    });
});