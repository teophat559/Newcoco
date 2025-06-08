<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Contestant Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <?php if ($contestant['image']): ?>
                <img src="<?php echo htmlspecialchars($contestant['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contestant['name']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($contestant['name']); ?></h1>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary">
                            <?php echo $contestant['vote_count']; ?> lượt bình chọn
                        </span>
                        <small class="text-muted">
                            Cuộc thi: <?php echo htmlspecialchars($contestant['contest_title']); ?>
                        </small>
                    </div>
                    <div class="card-text">
                        <?php echo nl2br(htmlspecialchars($contestant['description'])); ?>
                    </div>
                </div>
            </div>

            <!-- Contestant Gallery -->
            <?php if (!empty($contestant['gallery'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thư viện ảnh</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-4 g-4">
                        <?php foreach ($contestant['gallery'] as $image): ?>
                        <div class="col">
                            <a href="<?php echo htmlspecialchars($image['url']); ?>" data-lightbox="gallery">
                                <img src="<?php echo htmlspecialchars($image['thumbnail']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($contestant['name']); ?>">
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contestant Videos -->
            <?php if (!empty($contestant['videos'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Video</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <?php foreach ($contestant['videos'] as $video): ?>
                        <div class="col">
                            <div class="ratio ratio-16x9">
                                <iframe src="<?php echo htmlspecialchars($video['embed_url']); ?>"
                                        allowfullscreen></iframe>
                            </div>
                            <p class="mt-2"><?php echo htmlspecialchars($video['title']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Contest Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin cuộc thi</h5>
                </div>
                <div class="card-body">
                    <h6><?php echo htmlspecialchars($contestant['contest_title']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars(substr($contestant['contest_description'], 0, 150)) . '...'; ?></p>
                    <a href="/contests/<?php echo $contestant['contest_id']; ?>" class="btn btn-outline-primary btn-sm">Xem cuộc thi</a>
                </div>
            </div>

            <!-- Vote Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bình chọn</h5>
                </div>
                <div class="card-body">
                    <?php if ($contestant['contest_status'] === 'active'): ?>
                        <?php if ($canVote): ?>
                            <button class="btn btn-primary w-100 mb-2" onclick="vote(<?php echo $contestant['id']; ?>)">
                                <i class="fas fa-heart"></i> Bình chọn
                            </button>
                            <small class="text-muted d-block text-center">
                                Bạn còn <?php echo $remainingVotes; ?> lượt bình chọn hôm nay
                            </small>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                Bạn đã hết lượt bình chọn hôm nay
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>
                            Cuộc thi đã kết thúc
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Share Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Chia sẻ</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>"
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($contestant['name']); ?>"
                           class="btn btn-outline-info" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <button class="btn btn-outline-success" onclick="copyLink()">
                            <i class="fas fa-link"></i> Sao chép link
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vote History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử bình chọn</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($voteHistory as $vote): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($vote['created_at'])); ?>
                                </small>
                                <span class="badge bg-primary">
                                    <?php echo $vote['ip_address']; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function vote(contestantId) {
    fetch('/ajax/vote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            contestant_id: contestantId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Bình chọn thành công!');
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}

function copyLink() {
    const dummy = document.createElement('input');
    document.body.appendChild(dummy);
    dummy.value = window.location.href;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    alert('Đã sao chép link!');
}
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>