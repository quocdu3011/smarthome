<?php
require_once __DIR__ . '/Database.php';

class RFIDModel {
    private $db;
    private $usersTable = 'rfid_users';
    private $logsTable = 'rfid_logs';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsers($orderBy = 'created_at DESC') {
        $sql = "SELECT * FROM {$this->usersTable} ORDER BY {$orderBy}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->usersTable} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserByUID($uid) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->usersTable} WHERE uid = :uid LIMIT 1");
        $stmt->execute([':uid' => $uid]);
        return $stmt->fetch();
    }

    public function updateUser($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        $sql = "UPDATE {$this->usersTable} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->usersTable} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getLogs($start = null, $end = null, $uid = null, $limit = 100) {
        $params = [];
        $sql = "SELECT l.*, u.name 
                FROM {$this->logsTable} l 
                LEFT JOIN {$this->usersTable} u ON l.user_id = u.id 
                WHERE 1=1";

        if ($start) {
            $sql .= " AND l.logged_at >= :start";
            $params[':start'] = $start;
        }
        if ($end) {
            $sql .= " AND l.logged_at <= :end";
            $params[':end'] = $end;
        }
        if ($uid) {
            $sql .= " AND l.uid = :uid";
            $params[':uid'] = $uid;
        }

        $sql .= " ORDER BY l.logged_at DESC LIMIT :limit";
        $params[':limit'] = (int)$limit;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $val);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentLogs($limit = 10) {
        return $this->getLogs(null, null, null, $limit);
    }

    public function getRFIDCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->usersTable}");
        return $stmt->fetchColumn();
    }

    public function getLastScan() {
        $stmt = $this->db->query("SELECT logged_at FROM {$this->logsTable} ORDER BY logged_at DESC LIMIT 1");
        return $stmt->fetchColumn();
    }
}