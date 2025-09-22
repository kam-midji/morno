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

    // --- Remember Me Token Methods ---

    /**
     * Creates and stores a new "Remember Me" token for a user.
     * On success, returns an array containing the selector and the plain-text validator.
     * On failure, returns false.
     * @param int $userId The user's ID.
     * @return array|false
     */
    public function createRememberMeToken($userId) {
        // Delete any existing tokens for this user first.
        $this->deleteToken($userId);

        // Generate secure random tokens
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        // Set expiry for 30 days from now
        $expiresAt = date('Y-m-d H:i:s', time() + (86400 * 30));

        if ($this->insertToken($userId, $selector, $hashedValidator, $expiresAt)) {
            // Return plain-text tokens to be set in the cookie
            return ['selector' => $selector, 'validator' => $validator];
        }
        return false;
    }

    /**
     * Finds a token by its selector, only if it has not expired.
     * @param string $selector
     * @return mixed Token object if found, false otherwise.
     */
    public function findTokenBySelector($selector) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_tokens WHERE selector = :selector AND expires_at >= NOW()");
            $stmt->execute([':selector' => $selector]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deletes all "Remember Me" tokens for a given user ID.
     * This is useful for logging out from all devices or when creating a new token.
     * @param int $userId
     * @return bool
     */
    public function deleteToken($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE user_id = :user_id");
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Inserts a new token into the database.
     * @return bool True on success, false on failure.
     */
    private function insertToken($userId, $selector, $hashedValidator, $expiryDate) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO user_tokens (user_id, selector, hashed_validator, expires_at) VALUES (:user_id, :selector, :hashed_validator, :expires_at)"
            );
            return $stmt->execute([
                ':user_id' => $userId,
                ':selector' => $selector,
                ':hashed_validator' => $hashedValidator,
                ':expires_at' => $expiryDate
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Updates a token's selector and validator. Used for token rotation.
     * @return bool
     */
    public function updateToken($tokenId, $newSelector, $newHashedValidator, $expiryDate) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE user_tokens SET selector = :selector, hashed_validator = :hashed_validator, expires_at = :expires_at WHERE id = :id"
            );
            return $stmt->execute([
                ':id' => $tokenId,
                ':selector' => $newSelector,
                ':hashed_validator' => $newHashedValidator,
                ':expires_at' => $expiryDate,
            ]);
        } catch (PDOException $e) {
            return false;
        }
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
