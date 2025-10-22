<?php
require_once __DIR__ . '/Database.php';

class SensorModel
{
    private $db;
    private $table = 'sensor_data';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getLatestData($deviceId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($deviceId) {
            $sql .= " AND device_id = :device_id";
            $params[':device_id'] = $deviceId;
        }

        $sql .= " ORDER BY recorded_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getHistoricalData($start = null, $end = null, $deviceId = null, $interval = 'minute')
    {
        $groupBy = match ($interval) {
            'minute' => "DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i:00')",
            'hour' => "DATE_FORMAT(recorded_at, '%Y-%m-%d %H:00:00')",
            'day' => "DATE(recorded_at)",
            'month' => "DATE_FORMAT(recorded_at, '%Y-%m-01')",
            'year' => "DATE_FORMAT(recorded_at, '%Y-01-01')",
            default => "DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i:00')"
        };

        $sql = "SELECT 
                    {$groupBy} as timestamp,
                    ROUND(AVG(temp), 1) as temp,
                    ROUND(AVG(hum), 1) as hum,
                    ROUND(AVG(gas), 1) as gas,
                    ROUND(AVG(ldr), 1) as ldr,
                    ROUND(AVG(rain), 1) as rain
                FROM {$this->table} 
                WHERE 1=1";

        $params = [];
        if ($start) {
            $sql .= " AND recorded_at >= :start";
            $params[':start'] = $start;
        }
        if ($end) {
            $sql .= " AND recorded_at <= :end";
            $params[':end'] = $end;
        }
        if ($deviceId) {
            $sql .= " AND device_id = :device_id";
            $params[':device_id'] = $deviceId;
        }

        // Group by the same expression used in SELECT
        $sql .= " GROUP BY {$groupBy} ORDER BY {$groupBy} DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getHistoricalData: " . $e->getMessage());
            throw new Exception("Không thể lấy dữ liệu cảm biến: " . $e->getMessage());
        }
    }

    public function getRecentHistory($hours = 1, $deviceId = null)
    {
        $start = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        return $this->getHistoricalData($start, null, $deviceId);
    }

    public function getDailyAverages($days = 30, $deviceId = null)
    {
        $start = date('Y-m-d', strtotime("-{$days} days"));
        return $this->getHistoricalData($start, null, $deviceId, 'day');
    }

    public function getMonthlyAverages($months = 12, $deviceId = null)
    {
        $start = date('Y-m-d', strtotime("-{$months} months"));
        return $this->getHistoricalData($start, null, $deviceId, 'month');
    }

    public function getStatistics($start = null, $end = null, $deviceId = null)
    {
        $sql = "SELECT 
            MIN(temp) as min_temp,
            MAX(temp) as max_temp,
            AVG(temp) as avg_temp,
            MIN(hum) as min_hum,
            MAX(hum) as max_hum,
            AVG(hum) as avg_hum,
            MIN(gas) as min_gas,
            MAX(gas) as max_gas,
            AVG(gas) as avg_gas,
            MIN(ldr) as min_ldr,
            MAX(ldr) as max_ldr,
            AVG(ldr) as avg_ldr,
            MIN(rain) as min_rain,
            MAX(rain) as max_rain,
            AVG(rain) as avg_rain
        FROM {$this->table}
        WHERE 1=1";

        $params = [];

        // Thêm điều kiện thời gian
        if ($start) {
            $sql .= " AND recorded_at >= :start";
            $params[':start'] = $start;
        }
        if ($end) {
            $sql .= " AND recorded_at <= :end";
            $params[':end'] = $end;
        }

        // Thêm điều kiện device_id nếu có
        if ($deviceId) {
            $sql .= " AND device_id = :device_id";
            $params[':device_id'] = $deviceId;
        }

        // Nếu không có khoảng thời gian nào được chỉ định, mặc định lấy 24 giờ gần nhất
        if (!$start && !$end) {
            $defaultStart = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $sql .= " AND recorded_at >= :default_start";
            $params[':default_start'] = $defaultStart;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in getStatistics: " . $e->getMessage());
            throw new Exception("Không thể lấy thống kê cảm biến: " . $e->getMessage());
        }
    }

    public function insertData($data)
    {
        $fields = ['device_id', 'temp', 'hum', 'gas', 'ldr', 'rain', 'extra'];
        $placeholders = [];
        $values = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $placeholders[] = ":{$field}";
                $values[":{$field}"] = $data[$field];
            }
        }

        if (!empty($placeholders)) {
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        }

        return false;
    }
}