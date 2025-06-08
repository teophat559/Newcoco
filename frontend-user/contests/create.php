<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    set_flash_message('warning', 'Vui lòng đăng nhập để tạo cuộc thi');
    redirect(SITE_URL . '/auth/login.php');
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $max_participants = $_POST['max_participants'] ?? 0;
    $rules = $_POST['rules'] ?? '';
    $prizes = $_POST['prizes'] ?? '';

    $errors = [];

    // Validate dữ liệu
    if (empty($title)) {
        $errors[] = 'Vui lòng nhập tên cuộc thi';
    }

    if (empty($description)) {
        $errors[] = 'Vui lòng nhập mô tả cuộc thi';
    }

    if (empty($start_date)) {
        $errors[] = 'Vui lòng chọn ngày bắt đầu';
    }

    if (empty($end_date)) {
        $errors[] = 'Vui lòng chọn ngày kết thúc';
    }

    if ($start_date >= $end_date) {
        $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
    }

    if ($max_participants < 0) {
        $errors[] = 'Số lượng người tham gia không hợp lệ';
    }

    // Xử lý upload ảnh
    $banner = '';
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $banner = handle_file_upload($_FILES['banner'], 'contests');
        if (!$banner) {
            $errors[] = 'Có lỗi xảy ra khi upload ảnh';
        }
    }

    if (empty($errors)) {
        // Tạo cuộc thi mới
        $contest_id = create_contest([
            'title' => $title,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'max_participants' => $max_participants,
            'rules' => $rules,
            'prizes' => $prizes,
            'banner' => $banner,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($contest_id) {
            set_flash_message('success', 'Tạo cuộc thi thành công!');
            redirect(SITE_URL . '/contests/view.php?id=' . $contest_id);
        } else {
            set_flash_message('danger', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
    }

    if (!empty($errors)) {
        set_flash_message('danger', implode('<br>', $errors));
    }
}

$page_title = 'Tạo cuộc thi mới';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/contests">Cuộc thi</a></li>
                <li class="breadcrumb-item active">Tạo cuộc thi mới</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Tạo cuộc thi mới</h4>
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm('create-contest-form')">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tên cuộc thi</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="max_participants" class="form-label">Số lượng người tham gia tối đa</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" min="0">
                        <div class="form-text">Để trống nếu không giới hạn</div>
                    </div>

                    <div class="mb-3">
                        <label for="rules" class="form-label">Thể lệ cuộc thi</label>
                        <textarea class="form-control" id="rules" name="rules" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="prizes" class="form-label">Giải thưởng</label>
                        <textarea class="form-control" id="prizes" name="prizes" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="banner" class="form-label">Ảnh banner</label>
                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                        <div class="form-text">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Tạo cuộc thi
                        </button>
                        <a href="<?php echo SITE_URL; ?>/contests" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>