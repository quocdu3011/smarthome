<?php
require_once __DIR__ . '/../config.php';

class BackupManager {
    private $db;
    private $backupDir;
    private $tables = ['users', 'rfid_cards', 'devices', 'sensors', 'system_logs'];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->backupDir = __DIR__ . '/../backups/';
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function createBackup($includeData = true) {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = $this->backupDir . "backup_{$timestamp}.sql";
            $handle = fopen($filename, 'w+');

            foreach ($this->tables as $table) {
                // Get create table syntax
                $stmt = $this->db->prepare("SHOW CREATE TABLE {$table}");
                $stmt->execute();
                $row = $stmt->fetch();
                $createTable = $row['Create Table'] . ";\n\n";
                fwrite($handle, $createTable);

                if ($includeData) {
                    // Get table data
                    $stmt = $this->db->query("SELECT * FROM {$table}");
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return $this->db->quote($value);
                        }, $row);
                        
                        $insert = "INSERT INTO {$table} (" . 
                                 implode(', ', array_keys($row)) . 
                                 ") VALUES (" . 
                                 implode(', ', $values) . 
                                 ");\n";
                        fwrite($handle, $insert);
                    }
                    fwrite($handle, "\n");
                }
            }

            fclose($handle);
            return $filename;
        } catch (Exception $e) {
            throw new Exception("Backup failed: " . $e->getMessage());
        }
    }

    public function restoreFromBackup($backupFile) {
        try {
            if (!file_exists($backupFile)) {
                throw new Exception("Backup file not found");
            }

            $sql = file_get_contents($backupFile);
            $queries = explode(';', $sql);

            $this->db->beginTransaction();

            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $this->db->exec($query);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Restore failed: " . $e->getMessage());
        }
    }

    public function listBackups() {
        $backups = [];
        foreach (glob($this->backupDir . "backup_*.sql") as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'created' => date("Y-m-d H:i:s", filemtime($file))
            ];
        }
        return $backups;
    }

    public function deleteBackup($filename) {
        $file = $this->backupDir . basename($filename);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    public function scheduleBackup() {
        // Create a backup and delete backups older than 30 days
        $this->createBackup();
        
        $oldBackups = glob($this->backupDir . "backup_*.sql");
        foreach ($oldBackups as $backup) {
            if (filemtime($backup) < strtotime('-30 days')) {
                unlink($backup);
            }
        }
    }
}