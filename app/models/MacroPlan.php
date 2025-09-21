<?php

class MacroPlan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Gets all macro plan events for a given semester.
     * @param int $semesterId
     * @return array
     */
    public function getBySemester($semesterId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM macro_plan_events WHERE semester_id = :semester_id ORDER BY id ASC");
            $stmt->execute([':semester_id' => $semesterId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function add($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO macro_plan_events (semester_id, title, description) VALUES (:semester_id, :title, :description)");
            return $stmt->execute([
                ':semester_id' => $data['semester_id'],
                ':title' => $data['title'],
                ':description' => $data['description']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $stmt = $this->db->prepare("UPDATE macro_plan_events SET title = :title, description = :description WHERE id = :id");
            return $stmt->execute([
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':description' => $data['description']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM macro_plan_events WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM macro_plan_events WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }
}
