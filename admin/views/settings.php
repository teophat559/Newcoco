<?php
require_once __DIR__ . '/../includes/header.php';

// Get current settings
$settings = $settingsModel->getAllSettings();
?>

<div class="content-header">
    <h1>System Settings</h1>
</div>

<div class="content-body">
    <div class="card">
        <div class="card-body">
            <form id="settingsForm" method="POST" action="/admin/ajax/update_settings.php">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" class="form-control" id="site_name" name="site_name"
                           value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php
                        echo htmlspecialchars($settings['site_description']);
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" class="form-control" id="admin_email" name="admin_email"
                           value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="items_per_page">Items Per Page</label>
                    <input type="number" class="form-control" id="items_per_page" name="items_per_page"
                           value="<?php echo (int)$settings['items_per_page']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="max_file_size">Max File Size (MB)</label>
                    <input type="number" class="form-control" id="max_file_size" name="max_file_size"
                           value="<?php echo (int)$settings['max_file_size']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="allowed_extensions">Allowed File Extensions</label>
                    <input type="text" class="form-control" id="allowed_extensions" name="allowed_extensions"
                           value="<?php echo htmlspecialchars($settings['allowed_extensions']); ?>" required>
                    <small class="form-text text-muted">Comma-separated list (e.g., jpg,png,gif)</small>
                </div>

                <div class="form-group">
                    <label for="maintenance_mode">Maintenance Mode</label>
                    <select class="form-control" id="maintenance_mode" name="maintenance_mode">
                        <option value="0" <?php echo $settings['maintenance_mode'] ? '' : 'selected'; ?>>Off</option>
                        <option value="1" <?php echo $settings['maintenance_mode'] ? 'selected' : ''; ?>>On</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/admin/ajax/update_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings updated successfully');
        } else {
            alert(data.message || 'Error updating settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating settings');
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>