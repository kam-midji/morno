<?php

// Database Connection Handler
class Database {
    // --- IMPORTANT ---
    // These are placeholders. In a real application, these should be moved
    // to a configuration file that is NOT committed to version control.
    private $host = '127.0.0.1'; // or 'localhost'
    private $db_name = 'madreseh_planner';
    private $username = 'root';
    private $password = 'password';
    // --- /IMPORTANT ---

    private static $instance = null;
    private $conn;

    /**
     * Private constructor to prevent direct creation of object.
     */
    private function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // In a real app, you'd log this error, not display it to the user.
            die('Connection Failed: ' . $e->getMessage());
        }
    }

    /**
     * Gets the single instance of the database connection.
     * @return Database The single instance of the database connection.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Gets the PDO connection object.
     * @return PDO The PDO connection object.
     */
    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {}
}
