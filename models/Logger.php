<?php
require_once __DIR__ . '/../config.php';

class Logger {
    private $db;
    private $table = 'system_logs';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function log($type, $message, $userId = null, $details = null) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (log_type, message, user_id, details, created_at) 
            VALUES (:type, :message, :user_id, :details, CURRENT_TIMESTAMP)
        ");
        
        return $stmt->execute([
            ':type' => $type,
            ':message' => $message,
            ':user_id' => $userId,
            ':details' => is_array($details) ? json_encode($details) : $details
        ]);
    }

    public function getSystemLogs($startDate = null, $endDate = null, $type = null, $limit = 100) {
        $sql = "SELECT l.*, u.username 
                FROM {$this->table} l 
                LEFT JOIN users u ON l.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if ($startDate) {
            $sql .= " AND l.created_at >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND l.created_at <= :end_date";
            $params[':end_date'] = $endDate;
        }

        if ($type) {
            $sql .= " AND l.log_type = :type";
            $params[':type'] = $type;
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT :limit";
        $params[':limit'] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserActivityLogs($userId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->execute([':user_id' => $userId, ':limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function getErrorLogs($limit = 100) {
        return $this->getSystemLogs(null, null, 'error', $limit);
    }

    public function clearOldLogs($daysToKeep = 30) {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE created_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL :days DAY)
        ");
        return $stmt->execute([':days' => $daysToKeep]);
    }
}