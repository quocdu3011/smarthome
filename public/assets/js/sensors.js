/* Sensors Statistics JavaScript */

let sensorChart = null;
let startDatePicker = null;
let endDatePicker = null;
let currentData = null;

function loadSensorData() {
    if (!startDatePicker || !endDatePicker) return;

    const startDate = startDatePicker.selectedDates[0];
    const endDate = endDatePicker.selectedDates[0];
    
    if (!startDate || !endDate) return;

    const formatDateTime = date => {
        return date.getFullYear() + '-' + 
               String(date.getMonth() + 1).padStart(2, '0') + '-' +
               String(date.getDate()).padStart(2, '0') + ' ' +
               String(date.getHours()).padStart(2, '0') + ':' +
               String(date.getMinutes()).padStart(2, '0') + ':' +
               String(date.getSeconds()).padStart(2, '0');
    };

    const dates = {
        start: formatDateTime(startDate),
        end: formatDateTime(endDate)
    };
    const interval = document.getElementById('timeInterval').value;

    fetch('partials/process_sensors.php?' + new URLSearchParams({
        action: 'history',
        start: dates.start,
        end: dates.end,
        interval: interval
    }))
        .then(response => response.json())
        .then(data => {
            currentData = data;
            updateChart();
            updateTable();
        })
        .catch(error => {
            console.error('Error loading sensor data:', error);
            showToast('error', 'Không thể tải dữ liệu cảm biến');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Helper functions
    function formatDate(timestamp, format = 'dd/MM/yyyy HH:mm') {
        if (!timestamp) return '-';
        const date = new Date(timestamp);
        if (isNaN(date.getTime())) return '-';
        return format
            .replace(/yyyy/g, date.getFullYear())
            .replace(/MM/g, String(date.getMonth() + 1).padStart(2, '0'))
            .replace(/dd/g, String(date.getDate()).padStart(2, '0'))
            .replace(/HH/g, String(date.getHours()).padStart(2, '0'))
            .replace(/mm/g, String(date.getMinutes()).padStart(2, '0'))
            .replace(/ss/g, String(date.getSeconds()).padStart(2, '0'));
    }

    function formatNumber(value) {
        if (value === null || value === undefined || isNaN(value)) return '-';
        return Number(value).toFixed(1);
    }

    // Configure flatpickr
    flatpickr.localize(flatpickr.l10ns.vn);

    // Initialize datepicker for start date
    const now = new Date();
    const yesterday = new Date(now.getTime() - (24 * 60 * 60 * 1000));

    startDatePicker = flatpickr("#startDate", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        defaultDate: yesterday,
        onChange: function(selectedDates) {
            if (endDatePicker && selectedDates[0]) {
                endDatePicker.set('minDate', selectedDates[0]);
                loadSensorData();
                loadStatistics();
            }
        }
    });

    // Initialize datepicker for end date
    endDatePicker = flatpickr("#endDate", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        defaultDate: now,
        onChange: function(selectedDates) {
            if (startDatePicker && selectedDates[0]) {
                startDatePicker.set('maxDate', selectedDates[0]);
                loadSensorData();
                loadStatistics();
            }
        }
    });

    // Initialize chart
    const ctx = document.getElementById('sensorChart').getContext('2d');
    sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'hour',
                        displayFormats: {
                            minute: 'HH:mm',
                            hour: 'dd/MM HH:mm',
                            day: 'dd/MM/yyyy',
                            week: 'dd/MM/yyyy',
                            month: 'MM/yyyy',
                            quarter: 'MM/yyyy',
                            year: 'yyyy'
                        }
                    },
                    ticks: {
                        source: 'auto',
                        maxRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        padding: 10
                    }
                }
            }
        }
    });

    // Event listeners
    document.getElementById('chartType').addEventListener('change', updateChart);
    document.getElementById('timeInterval').addEventListener('change', loadSensorData);

    // Initial load
    loadSensorData();
    loadStatistics();

    // Refresh data periodically
    setInterval(loadSensorData, 60000); // every minute
    setInterval(loadStatistics, 300000); // every 5 minutes
});

function loadStatistics() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Convert date format từ d/m/Y H:i sang Y-m-d H:i:s
    const startParts = startDate.split(' ');
    const startDateParts = startParts[0].split('/');
    const formattedStart = `${startDateParts[2]}-${startDateParts[1]}-${startDateParts[0]} ${startParts[1]}:00`;
    
    const endParts = endDate.split(' ');
    const endDateParts = endParts[0].split('/');
    const formattedEnd = `${endDateParts[2]}-${endDateParts[1]}-${endDateParts[0]} ${endParts[1]}:00`;

    fetch('partials/process_sensors.php?' + new URLSearchParams({
        action: 'stats',
        start: formattedStart,
        end: formattedEnd
    }))
        .then(response => response.json())
        .then(updateStatistics)
        .catch(error => {
            console.error('Error loading statistics:', error);
            showToast('error', 'Không thể tải thống kê');
        });
}

function updateChart() {
    if (!currentData || !currentData.length) {
        sensorChart.data.labels = [];
        sensorChart.data.datasets = [];
        sensorChart.update();
        return;
    }

    const chartType = document.getElementById('chartType').value;
    const labels = currentData.map(d => {
        return d.timestamp ? new Date(d.timestamp.replace(' ', 'T')) : null;
    });
    let datasets = [];

    switch (chartType) {
        case 'temp-hum':
            datasets = [
                {
                    label: 'Nhiệt độ (°C)',
                    data: currentData.map(d => d.temp),
                    borderColor: '#ef4444',
                    tension: 0.4
                },
                {
                    label: 'Độ ẩm (%)',
                    data: currentData.map(d => d.hum),
                    borderColor: '#0ea5e9',
                    tension: 0.4
                }
            ];
            break;
        case 'gas':
            datasets = [{
                label: 'Khí gas',
                data: currentData.map(d => d.gas),
                borderColor: '#f59e0b',
                tension: 0.4
            }];
            break;
        case 'light':
            datasets = [{
                label: 'Ánh sáng',
                data: currentData.map(d => d.ldr),
                borderColor: '#eab308',
                tension: 0.4
            }];
            break;
        case 'rain':
            datasets = [{
                label: 'Mưa',
                data: currentData.map(d => d.rain),
                borderColor: '#3b82f6',
                tension: 0.4
            }];
            break;
    }

    sensorChart.data.labels = labels;
    sensorChart.data.datasets = datasets;
    sensorChart.update();
}

function updateTable() {
    const tbody = document.getElementById('sensorTable').querySelector('tbody');
    
    if (!currentData || !currentData.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">Không có dữ liệu</td>
            </tr>`;
        return;
    }

    tbody.innerHTML = currentData.map(row => {
        // Convert MySQL timestamp to local date
        const timestamp = row.timestamp ? new Date(row.timestamp.replace(' ', 'T') + 'Z') : null;
        return `
            <tr>
                <td>${formatDate(timestamp)}</td>
                <td>${formatNumber(row.temp)}°C</td>
                <td>${formatNumber(row.hum)}%</td>
                <td>${formatNumber(row.gas)}</td>
                <td>${formatNumber(row.ldr)}</td>
                <td>${row.rain === null ? '-' : formatNumber(row.rain)}</td>
            </tr>
        `;
    }).join('');
}

function updateStatistics(stats) {
    if (!stats) return;
    
    try {
        console.log('Updating statistics:', stats);
        // Temperature
        document.getElementById('minTemp').textContent = formatNumber(stats.min_temp) + '°C';
        document.getElementById('avgTemp').textContent = formatNumber(stats.avg_temp) + '°C';
        document.getElementById('maxTemp').textContent = formatNumber(stats.max_temp) + '°C';

        // Humidity
        document.getElementById('minHum').textContent = formatNumber(stats.min_hum) + '%';
        document.getElementById('avgHum').textContent = formatNumber(stats.avg_hum) + '%';
        document.getElementById('maxHum').textContent = formatNumber(stats.max_hum) + '%';

        // Gas
        document.getElementById('minGas').textContent = formatNumber(stats.min_gas);
        document.getElementById('avgGas').textContent = formatNumber(stats.avg_gas);
        document.getElementById('maxGas').textContent = formatNumber(stats.max_gas);

        // Light
        document.getElementById('minLight').textContent = formatNumber(stats.min_ldr);
        document.getElementById('avgLight').textContent = formatNumber(stats.avg_ldr);
        document.getElementById('maxLight').textContent = formatNumber(stats.max_ldr);
    } catch (error) {
        console.error('Error updating statistics:', error);
        showToast('error', 'Không thể cập nhật thống kê');
    }
}

function exportData() {
    if (!currentData || !currentData.length) {
        showToast('error', 'Không có dữ liệu để xuất');
        return;
    }

    const rows = [
        ['Thời gian', 'Nhiệt độ (°C)', 'Độ ẩm (%)', 'Khí gas', 'Ánh sáng', 'Mưa']
    ];

    currentData.forEach(row => {
        rows.push([
            formatDate(row.timestamp),
            row.temp,
            row.hum,
            row.gas,
            row.ldr,
            row.rain
        ]);
    });

    const csvContent = rows.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    link.href = URL.createObjectURL(blob);
    link.download = `sensor_data_${formatDate(new Date(), 'yyyy-MM-dd_HH-mm')}.csv`;
    link.style.display = 'none';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}