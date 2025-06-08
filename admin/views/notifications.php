<?php
require_once __DIR__ . '/../includes/header.php';

// Get notifications list
$notifications = $notificationModel->getAllNotifications();
?>

<div class="content-header">
    <h1>Manage Notifications</h1>
    <a href="/admin/notifications/create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Notification
    </a>
</div>

<div class="content-body">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?php echo $notification['id']; ?></td>
                            <td><?php echo htmlspecialchars($notification['title']); ?></td>
                            <td><?php echo ucfirst($notification['type']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $notification['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($notification['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/notifications/edit.php?id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/admin/notifications/view.php?id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/admin/ajax/delete_notification.php?id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting notification');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting notification');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>