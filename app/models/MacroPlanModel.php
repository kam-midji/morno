<?php

if (!class_exists('MacroPlanModel')) {
    class MacroPlanModel {
        private $db;

        public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Gets macro plan events, with an optional filter for a specific month.
     * @param int $semesterId
     * @param string|null $month YYYY-MM formatted string
     * @return array
     */
    public function getEvents($semesterId, $month = null) {
        $sql = "SELECT * FROM macro_plan_events WHERE semester_id = :semester_id";
        $params = [':semester_id' => $semesterId];

        if ($month) {
            $sql .= " AND DATE_FORMAT(event_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }

        $sql .= " ORDER BY event_date ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function add($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO macro_plan_events (semester_id, event_date, title, description) VALUES (:semester_id, :event_date, :title, :description)");
            return $stmt->execute([
                ':semester_id' => $data['semester_id'],
                ':event_date' => $data['event_date'],
                ':title' => $data['title'],
                ':description' => $data['description']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $stmt = $this->db->prepare("UPDATE macro_plan_events SET title = :title, description = :description, event_date = :event_date WHERE id = :id");
            return $stmt->execute([
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':event_date' => $data['event_date']
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
}
