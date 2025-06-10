class NotificationSound {
    constructor() {
        this.sounds = {
            user_login: new Audio('/sounds/user_login.mp3'),
            admin_login: new Audio('/sounds/admin_login.mp3'),
            new_visitor: new Audio('/sounds/new_visitor.mp3'),
            user_register: new Audio('/sounds/user_register.mp3'),
            admin_action: new Audio('/sounds/admin_action.mp3'),
            error_alert: new Audio('/sounds/error_alert.mp3')
        };

        this.messages = {
            user_login: {
                vi: 'Người dùng đã đăng nhập',
                en: 'User logged in'
            },
            admin_login: {
                vi: 'Quản trị viên đã đăng nhập',
                en: 'Admin logged in'
            },
            new_visitor: {
                vi: 'Khách truy cập mới',
                en: 'New visitor'
            },
            user_register: {
                vi: 'Người dùng mới đăng ký',
                en: 'New user registered'
            },
            admin_action: {
                vi: 'Hành động quản trị',
                en: 'Admin action'
            },
            error_alert: {
                vi: 'Cảnh báo lỗi',
                en: 'Error alert'
            }
        };
    }

    play(type) {
        if (this.sounds[type]) {
            this.sounds[type].play().catch(error => {
                console.error('Lỗi phát âm thanh thông báo:', error);
            });
        }
    }

    getMessage(type, lang = 'vi') {
        return this.messages[type]?.[lang] || this.messages[type]?.['en'] || type;
    }
}

// Khởi tạo xử lý âm thanh thông báo
const notificationSound = new NotificationSound();

// Hàm xử lý thông báo đến
function handleNotification(type, message, lang = 'vi') {
    // Phát âm thanh tương ứng
    notificationSound.play(type);

    // Hiển thị thông báo trong UI
    showNotificationUI(type, message || notificationSound.getMessage(type, lang), lang);
}

// Hàm hiển thị thông báo trong UI
function showNotificationUI(type, message, lang = 'vi') {
    const notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) return;

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const emoji = {
        user_login: '🔔',
        admin_login: '📢',
        new_visitor: '👋',
        user_register: '🎉',
        admin_action: '⚡',
        error_alert: '🚨'
    }[type] || '🔔';

    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-emoji">${emoji}</span>
            <span class="notification-message">${message}</span>
        </div>
    `;

    notificationContainer.appendChild(notification);

    // Xóa thông báo sau 5 giây
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Export để sử dụng trong các file khác
window.handleNotification = handleNotification;