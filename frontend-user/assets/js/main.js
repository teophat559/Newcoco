// Main JavaScript file

// DOM Elements
const notificationBell = document.querySelector('.notification-bell');
const notificationList = document.querySelector('.notification-list');
const voteButtons = document.querySelectorAll('.vote-btn');
const contestForms = document.querySelectorAll('.contest-form');

// Notification handling
function initNotifications() {
    if (notificationBell) {
        notificationBell.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const response = await fetch('/api/notifications');
                const data = await response.json();
                updateNotificationList(data);
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }
}

function updateNotificationList(notifications) {
    if (!notificationList) return;

    notificationList.innerHTML = notifications.map(notification => `
        <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.id}">
            <div class="notification-content">
                <div class="notification-title">${notification.message}</div>
                <div class="notification-time">${formatDate(notification.created_at)}</div>
            </div>
        </div>
    `).join('');
}

// Vote handling
function initVoteButtons() {
    voteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const contestId = button.dataset.contestId;
            const contestantId = button.dataset.contestantId;

            try {
                const response = await fetch(`/api/contests/${contestId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ contestant_id: contestantId })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('Vote submitted successfully!', 'success');
                    updateVoteCount(contestantId);
                } else {
                    showAlert(data.error || 'Failed to submit vote', 'danger');
                }
            } catch (error) {
                console.error('Error submitting vote:', error);
                showAlert('An error occurred while submitting your vote', 'danger');
            }
        });
    });
}

function updateVoteCount(contestantId) {
    const voteCount = document.querySelector(`#vote-count-${contestantId}`);
    if (voteCount) {
        const currentCount = parseInt(voteCount.textContent);
        voteCount.textContent = currentCount + 1;
    }
}

// Form handling
function initForms() {
    contestForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: form.method,
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message || 'Operation completed successfully!', 'success');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    showAlert(data.error || 'Operation failed', 'danger');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showAlert('An error occurred while processing your request', 'danger');
            }
        });
    });
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Image preview
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');

    imageInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = document.querySelector(`#${input.dataset.preview}`);

                if (preview) {
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    });
}

// Initialize all features
document.addEventListener('DOMContentLoaded', () => {
    initNotifications();
    initVoteButtons();
    initForms();
    initImagePreview();
});