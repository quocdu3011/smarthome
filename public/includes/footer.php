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
    <?php endif; ?>
    <script src="assets/js/main.js"></script>
    
    <!-- Custom page JS -->
    <?php if (isset($customJs)): ?>
    <script src="<?php echo $customJs; ?>"></script>
    <?php endif; ?>
</body>
</html>