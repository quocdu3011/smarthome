<?php
define('INCLUDED_FROM_INDEX', true);
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Thống kê cảm biến';
$currentPage = 'sensors';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Chart Controls -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <select class="form-select" id="chartType">
                        <option value="temp-hum">Nhiệt độ & Độ ẩm</option>
                        <option value="gas">Khí gas</option>
                        <option value="light">Ánh sáng</option>
                        <option value="rain">Mưa</option>
                    </select>
                    <label for="chartType">Loại dữ liệu</label>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-floating">
                    <select class="form-select" id="timeInterval">
                        <option value="minute">Theo phút</option>
                        <option value="hour">Theo giờ</option>
                        <option value="day">Theo ngày</option>
                        <option value="month">Theo tháng</option>
                    </select>
                    <label for="timeInterval">Đơn vị thời gian</label>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="input-group">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="startDate" placeholder="Thời gian bắt đầu">
                        <label for="startDate">Thời gian bắt đầu</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="endDate" placeholder="Thời gian kết thúc">
                        <label for="endDate">Thời gian kết thúc</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Chart -->
<div class="card mb-4">
    <div class="card-body">
        <canvas id="sensorChart" height="400"></canvas>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4">
    <!-- Temperature Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-temperature-high text-danger me-2"></i>
                    Nhiệt độ
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h3 id="minTemp">-</h3>
                        <small class="text-muted">Thấp nhất</small>
                    </div>
                    <div class="col">
                        <h3 id="avgTemp">-</h3>
                        <small class="text-muted">Trung bình</small>
                    </div>
                    <div class="col">
                        <h3 id="maxTemp">-</h3>
                        <small class="text-muted">Cao nhất</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Humidity Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-droplet text-info me-2"></i>
                    Độ ẩm
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h3 id="minHum">-</h3>
                        <small class="text-muted">Thấp nhất</small>
                    </div>
                    <div class="col">
                        <h3 id="avgHum">-</h3>
                        <small class="text-muted">Trung bình</small>
                    </div>
                    <div class="col">
                        <h3 id="maxHum">-</h3>
                        <small class="text-muted">Cao nhất</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gas Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-wind text-warning me-2"></i>
                    Khí gas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h3 id="minGas">-</h3>
                        <small class="text-muted">Thấp nhất</small>
                    </div>
                    <div class="col">
                        <h3 id="avgGas">-</h3>
                        <small class="text-muted">Trung bình</small>
                    </div>
                    <div class="col">
                        <h3 id="maxGas">-</h3>
                        <small class="text-muted">Cao nhất</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Light Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sun text-warning me-2"></i>
                    Ánh sáng
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h3 id="minLight">-</h3>
                        <small class="text-muted">Thấp nhất</small>
                    </div>
                    <div class="col">
                        <h3 id="avgLight">-</h3>
                        <small class="text-muted">Trung bình</small>
                    </div>
                    <div class="col">
                        <h3 id="maxLight">-</h3>
                        <small class="text-muted">Cao nhất</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Lịch sử dữ liệu</h5>
            <button class="btn btn-primary btn-sm" onclick="exportData()">
                <i class="fas fa-download me-1"></i>
                Xuất Excel
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="sensorTable">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Nhiệt độ (°C)</th>
                        <th>Độ ẩm (%)</th>
                        <th>Khí gas</th>
                        <th>Ánh sáng</th>
                        <th>Mưa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$useChart = true;
$customCss = 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css';
$customJs = 'assets/js/sensors.js';
include __DIR__ . '/../includes/footer.php';
?>