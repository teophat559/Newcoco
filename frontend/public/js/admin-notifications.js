class AdminNotificationManager {
    constructor() {
        this.notifications = [];
        this.autoDeleteTimeout = 30000; // 30 giây
        this.autoEditTimeout = 5000;    // 5 giây
    }

    // Thêm thông báo mới
    addNotification(type, message, lang = 'vi') {
        const notification = {
            id: Date.now(),
            type,
            message,
            lang,
            timestamp: new Date(),
            status: 'new'
        };

        this.notifications.unshift(notification);
        this.renderNotification(notification);
        this.setupAutoEdit(notification);
        this.setupAutoDelete(notification);
    }

    // Hiển thị thông báo
    renderNotification(notification) {
        const container = document.getElementById('admin-notification-container');
        if (!container) return;

        const notificationElement = document.createElement('div');
        notificationElement.id = `notification-${notification.id}`;
        notificationElement.className = `admin-notification notification-${notification.type}`;

        const emoji = {
            user_login: '🔔',
            admin_login: '📢',
            new_visitor: '👋',
            user_register: '🎉',
            admin_action: '⚡',
            error_alert: '🚨'
        }[notification.type] || '🔔';

        notificationElement.innerHTML = `
            <div class="notification-content">
                <span class="notification-emoji">${emoji}</span>
                <span class="notification-message">${notification.message}</span>
                <div class="notification-actions">
                    <button class="btn-edit" onclick="adminNotificationManager.editNotification(${notification.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="adminNotificationManager.deleteNotification(${notification.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="notification-status">
                <span class="status-badge status-${notification.status}">${this.getStatusText(notification.status, notification.lang)}</span>
                <span class="notification-time">${this.formatTime(notification.timestamp)}</span>
            </div>
        `;

        container.insertBefore(notificationElement, container.firstChild);
    }

    // Tự động chỉnh sửa thông báo
    setupAutoEdit(notification) {
        setTimeout(() => {
            if (notification.status === 'new') {
                notification.status = 'edited';
                this.updateNotificationUI(notification);
            }
        }, this.autoEditTimeout);
    }

    // Tự động xóa thông báo
    setupAutoDelete(notification) {
        setTimeout(() => {
            this.deleteNotification(notification.id);
        }, this.autoDeleteTimeout);
    }

    // Chỉnh sửa thông báo
    editNotification(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (!notification) return;

        const newMessage = prompt('Nhập nội dung mới:', notification.message);
        if (newMessage && newMessage !== notification.message) {
            notification.message = newMessage;
            notification.status = 'edited';
            notification.timestamp = new Date();
            this.updateNotificationUI(notification);
        }
    }

    // Xóa thông báo
    deleteNotification(id) {
        const element = document.getElementById(`notification-${id}`);
        if (element) {
            element.classList.add('notification-deleting');
            setTimeout(() => {
                element.remove();
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 300);
        }
    }

    // Cập nhật giao diện thông báo
    updateNotificationUI(notification) {
        const element = document.getElementById(`notification-${notification.id}`);
        if (!element) return;

        const statusBadge = element.querySelector('.status-badge');
        const timeElement = element.querySelector('.notification-time');

        if (statusBadge) {
            statusBadge.className = `status-badge status-${notification.status}`;
            statusBadge.textContent = this.getStatusText(notification.status, notification.lang);
        }

        if (timeElement) {
            timeElement.textContent = this.formatTime(notification.timestamp);
        }
    }

    // Lấy văn bản trạng thái
    getStatusText(status, lang) {
        const statusTexts = {
            new: {
                vi: 'Mới',
                en: 'New'
            },
            edited: {
                vi: 'Đã chỉnh sửa',
                en: 'Edited'
            }
        };
        return statusTexts[status]?.[lang] || statusTexts[status]?.['en'] || status;
    }

    // Định dạng thời gian
    formatTime(timestamp) {
        const now = new Date();
        const diff = now - timestamp;

        if (diff < 60000) { // Dưới 1 phút
            return 'Vừa xong';
        } else if (diff < 3600000) { // Dưới 1 giờ
            const minutes = Math.floor(diff / 60000);
            return `${minutes} phút trước`;
        } else {
            return timestamp.toLocaleTimeString();
        }
    }
}

// Khởi tạo quản lý thông báo admin
const adminNotificationManager = new AdminNotificationManager();

// Export để sử dụng trong các file khác
window.adminNotificationManager = adminNotificationManager;