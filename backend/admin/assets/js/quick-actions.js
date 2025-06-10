// Quick Actions JavaScript Functions

// Notification Settings
function saveNotificationSettings() {
    const form = document.getElementById('notificationSettingsForm');
    const formData = new FormData(form);
    const settings = {};

    for (let [key, value] of formData.entries()) {
        settings[key] = value;
    }

    // Gửi request lưu cài đặt
    fetch('/admin/api/settings/notifications', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Cài đặt thông báo đã được lưu');
            $('#notificationSettingsModal').modal('hide');
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Có lỗi xảy ra khi lưu cài đặt');
    });
}

// Generate Link
function generateLink() {
    const form = document.getElementById('generateLinkForm');
    const formData = new FormData(form);
    const data = {};

    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    fetch('/admin/api/links/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Link đã được tạo thành công');
            $('#generateLinkModal').modal('hide');
            // Hiển thị link mới tạo
            showGeneratedLink(data.link);
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Có lỗi xảy ra khi tạo link');
    });
}

// Block IP
function blockIP() {
    const form = document.getElementById('blockIPForm');
    const formData = new FormData(form);
    const data = {};

    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    fetch('/admin/api/security/block-ip', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'IP đã được chặn thành công');
            $('#blockIPModal').modal('hide');
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Có lỗi xảy ra khi chặn IP');
    });
}

// Social Integration
function saveSocialIntegration(platform) {
    const form = document.getElementById(`${platform}IntegrationForm`);
    const formData = new FormData(form);
    const data = {};

    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    fetch(`/admin/api/integrations/${platform}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', `Cài đặt ${platform} đã được lưu`);
            $(`#${platform}IntegrationModal`).modal('hide');
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', `Có lỗi xảy ra khi lưu cài đặt ${platform}`);
    });
}

// Helper Functions
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    const toastContainer = document.getElementById('toastContainer');
    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function showGeneratedLink(link) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'generatedLinkModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Link đã được tạo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" value="${link}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('${link}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Đã sao chép vào clipboard');
    }).catch(() => {
        showToast('error', 'Không thể sao chép vào clipboard');
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});