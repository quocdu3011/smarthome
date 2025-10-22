<?php
// Prevent direct access
if (!defined('INCLUDED_FROM_INDEX')) {
    die('Direct access not permitted');
}
?>
        </div><!-- /.main-content -->
    </div><!-- /.d-flex -->

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <?php if (isset($useChart)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <?php endif; ?>
    <?php if ($currentPage === 'rfid' || $currentPage === 'sensors'): ?>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <?php endif; ?>
    <!-- Load utilities first -->
    <script src="assets/js/utils.js"></script>

    <!-- Authentication handler -->
    <script src="assets/js/auth.js"></script>
    
    <!-- Then load other scripts -->
    <script src="assets/js/main.js"></script>
    
    <!-- Custom page JS -->
    <?php if (isset($customJs)): ?>
    <script src="<?php echo $customJs; ?>"></script>
    <?php endif; ?>
    
    <!-- Toast container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
</body>
</html>