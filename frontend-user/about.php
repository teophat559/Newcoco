<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Giới thiệu';

$content = '<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Giới thiệu về ' . SITE_NAME . '</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Sứ mệnh của chúng tôi</h2>
                    <p class="mb-0">
                        ' . SITE_NAME . ' được thành lập với sứ mệnh tạo ra một nền tảng bình chọn công bằng,
                        minh bạch và dễ tiếp cận cho tất cả mọi người. Chúng tôi tin rằng mỗi người đều có
                        quyền được lắng nghe và thể hiện ý kiến của mình.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Tầm nhìn</h2>
                    <p class="mb-0">
                        Chúng tôi mong muốn trở thành nền tảng bình chọn hàng đầu, nơi mọi người có thể
                        tham gia vào các cuộc thi một cách công bằng và minh bạch. Chúng tôi cam kết
                        không ngừng cải thiện và phát triển để mang đến trải nghiệm tốt nhất cho người dùng.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Giá trị cốt lõi</h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Công bằng và minh bạch trong mọi hoạt động
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Tôn trọng quyền riêng tư của người dùng
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Không ngừng đổi mới và cải thiện
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Hỗ trợ và phát triển cộng đồng
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="h4 mb-3">Liên hệ</h2>
                    <p class="mb-0">
                        Nếu bạn có bất kỳ câu hỏi hoặc góp ý nào, vui lòng liên hệ với chúng tôi:<br>
                        <a href="mailto:' . SITE_EMAIL . '">
                            <i class="fas fa-envelope me-2"></i>' . SITE_EMAIL . '
                        </a><br>
                        <a href="tel:' . SITE_PHONE . '">
                            <i class="fas fa-phone me-2"></i>' . SITE_PHONE . '
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>';

require_once __DIR__ . '/includes/header.php';
echo $content;
require_once __DIR__ . '/includes/footer.php';
