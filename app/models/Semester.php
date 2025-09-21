<?php

class Semester {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        try {
            // Order by start date descending to show newest first
            $stmt = $this->db->query("SELECT * FROM semesters ORDER BY start_date DESC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM semesters WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (:name, :start_date, :end_date)");
            return $stmt->execute([
                ':name' => $data['name'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $stmt = $this->db->prepare("UPDATE semesters SET name = :name, start_date = :start_date, end_date = :end_date WHERE id = :id");
            return $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function archive($id) {
        try {
            $stmt = $this->db->prepare("UPDATE semesters SET is_archived = 1 WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Gets the current, un-archived semester.
     * For simplicity, we'll define "current" as the un-archived semester with the most recent start date.
     */
    public function getCurrent() {
        try {
            $stmt = $this->db->query("SELECT * FROM semesters WHERE is_archived = 0 ORDER BY start_date DESC LIMIT 1");
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }
}
