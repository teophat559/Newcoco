<?php
// Quick Action Modals
function render_quick_action_modals() {
    ?>
    <!-- Notification Settings Modal -->
    <div class="modal fade" id="notificationSettingsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cài Đặt Thông Báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="notificationSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">Phương thức thông báo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_popup" id="notifyPopup">
                                <label class="form-check-label" for="notifyPopup">Popup thông báo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_telegram" id="notifyTelegram">
                                <label class="form-check-label" for="notifyTelegram">Telegram</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_sound" id="notifySound">
                                <label class="form-check-label" for="notifySound">Âm thanh</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại thông báo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_new_user" id="notifyNewUser">
                                <label class="form-check-label" for="notifyNewUser">Người dùng mới</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_new_contest" id="notifyNewContest">
                                <label class="form-check-label" for="notifyNewContest">Cuộc thi mới</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_new_vote" id="notifyNewVote">
                                <label class="form-check-label" for="notifyNewVote">Bình chọn mới</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveNotificationSettings()">Lưu cài đặt</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Link Modal -->
    <div class="modal fade" id="generateLinkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo Tên Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="generateLinkForm">
                        <div class="mb-3">
                            <label class="form-label">Loại link</label>
                            <select class="form-select" name="link_type" required>
                                <option value="admin">Admin</option>
                                <option value="contest">Cuộc thi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên link</label>
                            <input type="text" class="form-control" name="link_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời hạn</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success" onclick="generateLink()">Tạo link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Block IP Modal -->
    <div class="modal fade" id="blockIPModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chặn IP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="blockIPForm">
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ IP</label>
                            <input type="text" class="form-control" name="ip_address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lý do chặn</label>
                            <textarea class="form-control" name="block_reason" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời hạn chặn</label>
                            <select class="form-select" name="block_duration">
                                <option value="1">1 giờ</option>
                                <option value="24">24 giờ</option>
                                <option value="168">1 tuần</option>
                                <option value="720">1 tháng</option>
                                <option value="0">Vĩnh viễn</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" onclick="blockIP()">Chặn IP</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Integration Modals -->
    <?php
    $social_platforms = [
        'facebook' => ['title' => 'Facebook', 'icon' => 'bi-facebook'],
        'instagram' => ['title' => 'Instagram', 'icon' => 'bi-instagram'],
        'gmail' => ['title' => 'Gmail', 'icon' => 'bi-envelope'],
        'yahoo' => ['title' => 'Yahoo', 'icon' => 'bi-envelope-fill'],
        'zalo' => ['title' => 'Zalo', 'icon' => 'bi-chat-dots']
    ];

    foreach ($social_platforms as $platform => $info):
    ?>
    <div class="modal fade" id="<?php echo $platform; ?>IntegrationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tích hợp <?php echo $info['title']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="<?php echo $platform; ?>IntegrationForm">
                        <div class="mb-3">
                            <label class="form-label">App ID</label>
                            <input type="text" class="form-control" name="app_id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">App Secret</label>
                            <input type="password" class="form-control" name="app_secret" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Callback URL</label>
                            <input type="url" class="form-control" name="callback_url" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_login" id="<?php echo $platform; ?>Login">
                                <label class="form-check-label" for="<?php echo $platform; ?>Login">Cho phép đăng nhập</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_otp" id="<?php echo $platform; ?>OTP">
                                <label class="form-check-label" for="<?php echo $platform; ?>OTP">Gửi OTP</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_notification" id="<?php echo $platform; ?>Notification">
                                <label class="form-check-label" for="<?php echo $platform; ?>Notification">Gửi thông báo</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="save<?php echo ucfirst($platform); ?>Integration()">Lưu cài đặt</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php
}
?>