<?php
define('INCLUDED_FROM_INDEX', true);
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Bảng điều khiển';
$currentPage = 'dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="row g-3">
    <!-- RFID Summary Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Thẻ RFID</h6>
                        <h2 class="card-title mb-0" id="rfidCount">-</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-id-card fa-2x text-primary"></i>
                    </div>
                </div>
                <p class="card-text mt-3 mb-0">
                    <small class="text-muted">Thẻ được quét gần đây: </small>
                    <span id="lastRFIDScan">-</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Temperature Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Nhiệt độ</h6>
                        <h2 class="card-title mb-0"><span id="currentTemp">-</span>°C</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-temperature-high fa-2x text-warning"></i>
                    </div>
                </div>
                <p class="card-text mt-3 mb-0">
                    <small class="text-muted">Cập nhật: </small>
                    <span id="tempUpdated">-</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Humidity Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Độ ẩm</h6>
                        <h2 class="card-title mb-0"><span id="currentHum">-</span>%</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-droplet fa-2x text-info"></i>
                    </div>
                </div>
                <p class="card-text mt-3 mb-0">
                    <small class="text-muted">Cập nhật: </small>
                    <span id="humUpdated">-</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Devices Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle text-muted">Thiết bị</h6>
                        <h2 class="card-title mb-0" id="deviceCount">-</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-microchip fa-2x text-success"></i>
                    </div>
                </div>
                <p class="card-text mt-3 mb-0">
                    <small class="text-muted">Hoạt động: </small>
                    <span id="activeDevices">-</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <!-- Temperature & Humidity Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Nhiệt độ & Độ ẩm</h5>
            </div>
            <div class="card-body">
                <canvas id="tempHumChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- RFID Access Log -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lịch sử quét thẻ gần đây</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="recentRFIDLogs">
                    <!-- RFID logs will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$useChart = true;
$customJs = 'assets/js/dashboard.js';
include __DIR__ . '/../includes/footer.php';
?>