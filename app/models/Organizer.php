<?php

class Organizer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM organizers ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM organizers WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO organizers (name) VALUES (:name)");
            return $stmt->execute([':name' => $data['name']]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $stmt = $this->db->prepare("UPDATE organizers SET name = :name WHERE id = :id");
            return $stmt->execute([':id' => $data['id'], ':name' => $data['name']]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM organizers WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
