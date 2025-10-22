/* Dashboard JavaScript */

let tempHumChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart
    const ctx = document.getElementById('tempHumChart').getContext('2d');
    tempHumChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Nhiệt độ (°C)',
                    data: [],
                    borderColor: '#fbbf24',
                    tension: 0.4
                },
                {
                    label: 'Độ ẩm (%)',
                    data: [],
                    borderColor: '#0ea5e9',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Load initial data
    loadDashboardData();
    loadRecentRFIDLogs();

    // Refresh data periodically
    setInterval(loadDashboardData, 30000); // every 30 seconds
    setInterval(loadRecentRFIDLogs, 60000); // every minute
});

function loadDashboardData() {
    // Load summary data
    fetch('partials/process_stats.php')
        .then(response => response.json())
        .then(data => {
            updateSummaryCards(data);
            updateChart(data.tempHumHistory);
        })
        .catch(error => console.error('Error loading dashboard data:', error));
}

function loadRecentRFIDLogs() {
    fetch('partials/process_rfid.php?action=recent')
        .then(response => response.json())
        .then(data => {
            updateRFIDLogs(data);
        })
        .catch(error => console.error('Error loading RFID logs:', error));
}

function updateSummaryCards(data) {
    // Update RFID card
    document.getElementById('rfidCount').textContent = data.rfidCount;
    document.getElementById('lastRFIDScan').textContent = data.lastRFIDScan ? 
        formatDate(data.lastRFIDScan) : 'Chưa có';

    // Update Temperature card
    document.getElementById('currentTemp').textContent = data.currentTemp?.toFixed(1) ?? '-';
    document.getElementById('tempUpdated').textContent = data.tempUpdated ? 
        formatDate(data.tempUpdated) : '-';

    // Update Humidity card
    document.getElementById('currentHum').textContent = data.currentHum?.toFixed(1) ?? '-';
    document.getElementById('humUpdated').textContent = data.humUpdated ? 
        formatDate(data.humUpdated) : '-';

    // Update Devices card
    document.getElementById('deviceCount').textContent = data.deviceCount;
    document.getElementById('activeDevices').textContent = data.activeDevices + 
        ' / ' + data.deviceCount;
}

function updateChart(history) {
    if (!history || !history.length) return;

    const labels = history.map(item => formatDate(item.recorded_at, 'HH:mm'));
    const temps = history.map(item => item.temp);
    const hums = history.map(item => item.hum);

    tempHumChart.data.labels = labels;
    tempHumChart.data.datasets[0].data = temps;
    tempHumChart.data.datasets[1].data = hums;
    tempHumChart.update();
}

function updateRFIDLogs(logs) {
    const container = document.getElementById('recentRFIDLogs');
    container.innerHTML = '';

    if (!logs || !logs.length) {
        container.innerHTML = `
            <div class="list-group-item text-center text-muted">
                Chưa có lịch sử quét thẻ
            </div>
        `;
        return;
    }

    logs.forEach(log => {
        const item = document.createElement('div');
        item.className = 'list-group-item';
        
        const status = log.user_id ? 
            '<span class="badge bg-success">Hợp lệ</span>' : 
            '<span class="badge bg-danger">Không hợp lệ</span>';

        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${log.user_id ? log.name : 'Không xác định'}</h6>
                    <small class="text-muted">ID: ${log.uid}</small>
                </div>
                <div class="text-end">
                    ${status}
                    <div><small class="text-muted">${formatDate(log.logged_at)}</small></div>
                </div>
            </div>
        `;
        container.appendChild(item);
    });
}