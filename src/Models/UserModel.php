<?php

namespace SmartHome\Models;

class UserModel {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function validateUser($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username AND status = 'active' LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }

    public function createUser($username, $password, $email, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (username, password, email, role, status, created_at) 
            VALUES (:username, :password, :email, :role, 'active', CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':role' => $role
        ]);
    }

    public function getUsers() {
        $stmt = $this->db->query("SELECT id, username, email, role, status, created_at, last_login FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, role, status, created_at, last_login FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT id, username, email, role, status, created_at, last_login FROM {$this->table} WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT id, username, email, role, status, created_at, last_login FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function updateUser($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'username') {
                if ($key === 'password') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive' WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}