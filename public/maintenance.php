<?php
require_once '../config.php';
require_once '../models/Logger.php';
require_once '../models/BackupManager.php';

// Initialize managers
$logger = new Logger();
$backupManager = new BackupManager();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_backup':
                try {
                    $backupFile = $backupManager->createBackup();
                    $_SESSION['success'] = "Backup created successfully: " . basename($backupFile);
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
                break;

            case 'restore_backup':
                if (isset($_POST['backup_file'])) {
                    try {
                        $backupManager->restoreFromBackup($_POST['backup_file']);
                        $_SESSION['success'] = "System restored successfully from backup";
                    } catch (Exception $e) {
                        $_SESSION['error'] = $e->getMessage();
                    }
                }
                break;

            case 'delete_backup':
                if (isset($_POST['backup_file'])) {
                    try {
                        $backupManager->deleteBackup($_POST['backup_file']);
                        $_SESSION['success'] = "Backup deleted successfully";
                    } catch (Exception $e) {
                        $_SESSION['error'] = $e->getMessage();
                    }
                }
                break;

            case 'clear_logs':
                try {
                    $logger->clearOldLogs($_POST['days'] ?? 30);
                    $_SESSION['success'] = "Old logs cleared successfully";
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
                break;
        }
    }
    header('Location: maintenance.php');
    exit;
}

// Get logs and backups
$systemLogs = $logger->getSystemLogs();
$errorLogs = $logger->getErrorLogs();
$backups = $backupManager->listBackups();

// Include header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">System Maintenance</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Backup Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Backup Management</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-3">
                        <input type="hidden" name="action" value="create_backup">
                        <button type="submit" class="btn btn-primary">Create New Backup</button>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($backup['filename']); ?></td>
                                <td><?php echo number_format($backup['size'] / 1024, 2); ?> KB</td>
                                <td><?php echo $backup['created']; ?></td>
                                <td>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="restore_backup">
                                        <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to restore this backup? This will overwrite current data.')">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="delete_backup">
                                        <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this backup?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System Logs -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>System Logs</h4>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="action" value="clear_logs">
                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to clear old logs?')">
                            Clear Old Logs
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th>Message</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($systemLogs as $log): ?>
                                <tr class="<?php echo $log['log_type'] === 'error' ? 'table-danger' : ''; ?>">
                                    <td><?php echo $log['created_at']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($log['log_type']) {
                                                'error' => 'danger',
                                                'warning' => 'warning',
                                                'info' => 'info',
                                                'security' => 'primary',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($log['log_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                    <td><?php echo htmlspecialchars($log['message']); ?></td>
                                    <td>
                                        <?php if ($log['details']): ?>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logDetails<?php echo $log['id']; ?>">
                                                View
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Log Details Modals -->
<?php foreach ($systemLogs as $log): ?>
<?php if ($log['details']): ?>
<div class="modal fade" id="logDetails<?php echo $log['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre><?php echo htmlspecialchars($log['details']); ?></pre>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>