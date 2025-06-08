<link rel="stylesheet" href="/frontend-user/assets/css/bootstrap.min.css">
<script src="/frontend-user/assets/js/media-api.js"></script>
<div class="container py-4">
    <h2 class="mb-4">Quản lý Media</h2>
    <form id="media-upload-form" enctype="multipart/form-data" class="mb-4 row g-2 align-items-end">
        <div class="col-md-3">
            <input type="file" class="form-control" name="file" required>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="title" placeholder="Tiêu đề">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="description" placeholder="Mô tả">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="category" placeholder="Danh mục">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Tải lên</button>
        </div>
    </form>
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="media-search" class="form-control" placeholder="Tìm kiếm file...">
        </div>
        <div class="col-md-2">
            <button id="media-search-btn" class="btn btn-primary">Tìm kiếm</button>
        </div>
    </div>
    <div class="row" id="media-list"></div>
</div>