<?php
// Quick Actions Component
function render_quick_actions() {
    $actions = [
        // System Actions
        [
            'icon' => 'bi-bell',
            'title' => 'Cài Đặt Thông Báo',
            'description' => 'Cấu hình cách gửi thông báo đến user',
            'modal_id' => 'notificationSettingsModal',
            'color' => 'primary',
            'group' => 'system'
        ],
        [
            'icon' => 'bi-link-45deg',
            'title' => 'Tạo Tên Link',
            'description' => 'Sinh link phụ cho từng admin/cuộc thi',
            'modal_id' => 'generateLinkModal',
            'color' => 'success',
            'group' => 'system'
        ],
        [
            'icon' => 'bi-shield-lock',
            'title' => 'Chặn IP',
            'description' => 'Chặn địa chỉ IP truy cập không hợp lệ',
            'modal_id' => 'blockIPModal',
            'color' => 'danger',
            'group' => 'system'
        ],
        // Social Media Actions
        [
            'icon' => 'bi-facebook',
            'title' => 'Facebook',
            'description' => 'Tích hợp đăng nhập Facebook',
            'modal_id' => 'facebookIntegrationModal',
            'color' => 'info',
            'group' => 'social'
        ],
        [
            'icon' => 'bi-instagram',
            'title' => 'Instagram',
            'description' => 'Tích hợp đăng nhập Instagram',
            'modal_id' => 'instagramIntegrationModal',
            'color' => 'warning',
            'group' => 'social'
        ],
        [
            'icon' => 'bi-envelope',
            'title' => 'Gmail',
            'description' => 'Tích hợp đăng nhập Gmail',
            'modal_id' => 'gmailIntegrationModal',
            'color' => 'danger',
            'group' => 'social'
        ],
        [
            'icon' => 'bi-envelope-fill',
            'title' => 'Yahoo',
            'description' => 'Tích hợp đăng nhập Yahoo',
            'modal_id' => 'yahooIntegrationModal',
            'color' => 'primary',
            'group' => 'social'
        ],
        [
            'icon' => 'bi-chat-dots',
            'title' => 'Zalo',
            'description' => 'Tích hợp đăng nhập Zalo',
            'modal_id' => 'zaloIntegrationModal',
            'color' => 'info',
            'group' => 'social'
        ]
    ];

    // Group actions by type
    $grouped_actions = [];
    foreach ($actions as $action) {
        $grouped_actions[$action['group']][] = $action;
    }
    ?>
    <div class="quick-actions mb-4">
        <!-- System Actions -->
        <div class="row g-3 mb-4">
            <?php foreach ($grouped_actions['system'] as $action): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi <?php echo $action['icon']; ?> fs-1 text-<?php echo $action['color']; ?>"></i>
                        </div>
                        <h5 class="card-title"><?php echo $action['title']; ?></h5>
                        <p class="card-text small text-muted"><?php echo $action['description']; ?></p>
                        <button type="button" class="btn btn-<?php echo $action['color']; ?> btn-sm"
                                data-bs-toggle="modal" data-bs-target="#<?php echo $action['modal_id']; ?>">
                            Cấu hình
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Social Media Actions -->
        <h5 class="mb-3">Tích hợp mạng xã hội</h5>
        <div class="row g-3">
            <?php foreach ($grouped_actions['social'] as $action): ?>
            <div class="col-md-3 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi <?php echo $action['icon']; ?> fs-1 text-<?php echo $action['color']; ?>"></i>
                        </div>
                        <h5 class="card-title"><?php echo $action['title']; ?></h5>
                        <p class="card-text small text-muted"><?php echo $action['description']; ?></p>
                        <button type="button" class="btn btn-<?php echo $action['color']; ?> btn-sm"
                                data-bs-toggle="modal" data-bs-target="#<?php echo $action['modal_id']; ?>">
                            Cấu hình
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
    .quick-actions .card {
        transition: transform 0.2s ease-in-out;
    }
    .quick-actions .card:hover {
        transform: translateY(-5px);
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    .quick-actions .card-title {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .quick-actions .card-text {
        font-size: 0.875rem;
        min-height: 2.5rem;
    }
    </style>
    <?php
}
?>