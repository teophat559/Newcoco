        </div><!-- /.content -->
    </div><!-- /.main-content -->

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <div class="footer-links">
                <a href="/admin/privacy.php">Privacy Policy</a>
                <a href="/admin/terms.php">Terms of Service</a>
                <a href="/admin/help.php">Help & Support</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Toggle sidebar
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Dropdown menu
        document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.dropdown').classList.toggle('show');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(function(dropdown) {
                    dropdown.classList.remove('show');
                });
            }
        });

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                alert.classList.remove('show');
                setTimeout(function() {
                    alert.remove();
                }, 150);
            }, 5000);
        });
    </script>
</body>
</html>