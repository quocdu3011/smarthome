<?php
require_once __DIR__ . '/Database.php';

class DeviceModel {
    private $db;
    private $table = 'devices';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllDevices() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getDevice($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getDeviceByApiKey($apiKey) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE api_key = :key LIMIT 1");
        $stmt->execute([':key' => $apiKey]);
        return $stmt->fetch();
    }

    public function createDevice($deviceName) {
        $apiKey = bin2hex(random_bytes(16)); // 32 characters
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (device_name, api_key) VALUES (:name, :key)");
        if ($stmt->execute([
            ':name' => $deviceName,
            ':key' => $apiKey
        ])) {
            return [
                'id' => $this->db->lastInsertId(),
                'device_name' => $deviceName,
                'api_key' => $apiKey
            ];
        }
        return false;
    }

    public function updateDevice($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'api_key') { // Don't allow API key updates
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteDevice($id) {
        // First check if device has any sensor data
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sensor_data WHERE device_id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            // If device has data, just update api_key to null to prevent future access
            $stmt = $this->db->prepare("UPDATE {$this->table} SET api_key = NULL WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } else {
            // If no data, we can safely delete the device
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        }
    }

    public function regenerateApiKey($id) {
        $newApiKey = bin2hex(random_bytes(16));
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET api_key = :key WHERE id = :id");
        if ($stmt->execute([
            ':key' => $newApiKey,
            ':id' => $id
        ])) {
            return $newApiKey;
        }
        return false;
    }

    public function getDeviceCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function getActiveDevices($timeWindow = '1 hour') {
        $sql = "SELECT COUNT(DISTINCT d.id) 
                FROM {$this->table} d
                INNER JOIN sensor_data sd ON d.id = sd.device_id
                WHERE sd.recorded_at >= DATE_SUB(NOW(), INTERVAL {$timeWindow})";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function getDeviceStats($deviceId) {
        $stmt = $this->db->prepare("
            SELECT MAX(recorded_at) as last_data, COUNT(*) as data_count
            FROM sensor_data
            WHERE device_id = :id
        ");
        $stmt->execute([':id' => $deviceId]);
        return $stmt->fetch();
    }
}