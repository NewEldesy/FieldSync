<?php if (isset($_SESSION['user_id'])): ?>
        </main><!-- /main-content -->
    </div><!-- /main-wrapper -->
<?php else: ?>
        </div><!-- /auth-container -->
    </div><!-- /auth-wrapper -->
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<?php if (isset($_SESSION['user_id'])): ?>
<script>
(function() {
    var sidebar  = document.getElementById('sidebar');
    var wrapper  = document.getElementById('mainWrapper');
    var toggle   = document.getElementById('sidebarToggle');
    var overlay  = document.getElementById('sidebarOverlay');
    var STORAGE  = 'fs_sidebar_collapsed';

    /* Restore saved state */
    if (localStorage.getItem(STORAGE) === '1') {
        sidebar.classList.add('collapsed');
    }

    function isMobile() { return window.innerWidth < 769; }

    if (toggle) {
        toggle.addEventListener('click', function () {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                var collapsed = sidebar.classList.toggle('collapsed');
                localStorage.setItem(STORAGE, collapsed ? '1' : '0');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }
})();
</script>
<?php endif; ?>

</body>
</html>
