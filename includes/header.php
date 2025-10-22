<?php
// Prevent direct access
if (!defined('INCLUDED_FROM_INDEX')) {
    die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['token'] ?? ''; ?>">
    <title><?php echo $pageTitle ?? 'Smart Home'; ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <?php if (isset($useChart)): ?>
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <?php endif; ?>
    <?php if ($currentPage === 'rfid' || $currentPage === 'sensors'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <?php endif; ?>
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Custom page CSS -->
    <?php if (isset($customCss)): ?>
    <link href="<?php echo $customCss; ?>" rel="stylesheet">
    <?php endif; ?>
</head>
<body>
    <div class="d-flex">