<?php

if (!class_exists('User')) {
    class User {
        private $db;

        public function __construct() {
        // In the future, a dependency injection container would be better,
        // but for now, we get the DB instance directly.
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find user by username.
     * @param string $username
     * @return mixed User object if found, false otherwise.
     */
    public function findByUsername($username) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // In a real app, log the error.
            return false;
        }
    }

    /**
     * Find user by ID.
     * @param int $id
     * @return mixed User object if found, false otherwise.
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    // --- Remember Me Token Methods (to be implemented later) ---

    public function findTokenBySelector($selector) {
        // Placeholder
        return false;
    }

    public function storeToken($userId, $selector, $hashedValidator, $expiryDate) {
        // Placeholder
        return true;
    }

    public function deleteToken($userId) {
        // Placeholder
        return true;
    }

    public function getUsers() {
        try {
            $stmt = $this->db->query("SELECT id, username, full_name, role FROM users ORDER BY role, username ASC");
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $results;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function usernameExists($username) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn() > 0;
    }

    public function register($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (full_name, username, password, role) VALUES (:full_name, :username, :password, :role)"
            );
            $stmt->execute([
                ':full_name' => $data['full_name'],
                ':username' => $data['username'],
                ':password' => $data['password'], // Password should be hashed before calling this
                ':role' => $data['role']
            ]);
            return true;
        } catch (PDOException $e) {
            // In a real app, log error
            return false;
        }
    }

    public function updateUser($data) {
        try {
            // Check if password needs to be updated
            if (!empty($data['password'])) {
                $sql = "UPDATE users SET full_name = :full_name, username = :username, password = :password, role = :role WHERE id = :id";
                $params = [
                    ':id' => $data['id'],
                    ':full_name' => $data['full_name'],
                    ':username' => $data['username'],
                    ':password' => $data['password'], // Assumes password is ALREADY HASHED
                    ':role' => $data['role']
                ];
            } else {
                // Update without changing password
                $sql = "UPDATE users SET full_name = :full_name, username = :username, role = :role WHERE id = :id";
                $params = [
                    ':id' => $data['id'],
                    ':full_name' => $data['full_name'],
                    ':username' => $data['username'],
                    ':role' => $data['role']
                ];
            }
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
}
