<footer class="footer mt-auto py-3 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-0">
                    Version <?php echo APP_VERSION; ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
// Enable tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

// Enable popovers
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
})

// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Confirm delete actions
document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục này?')) {
                e.preventDefault();
            }
        });
    });
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>