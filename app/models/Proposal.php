<?php

class Proposal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Adds a new proposal and its associated audiences/organizers.
     * This will be a transaction to ensure data integrity.
     * @param array $data The proposal data.
     * @return bool True on success, false on failure.
     */
    public function add($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into proposals table
            $stmt = $this->db->prepare(
                "INSERT INTO proposals (user_id, semester_id, title, event_datetime, event_end_datetime, objective, priority)
                 VALUES (:user_id, :semester_id, :title, :event_datetime, :event_end_datetime, :objective, :priority)"
            );
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':semester_id' => $data['semester_id'],
                ':title' => $data['title'],
                ':event_datetime' => $data['event_datetime'],
                ':event_end_datetime' => $data['event_end_datetime'],
                ':objective' => $data['objective'],
                ':priority' => $data['priority']
            ]);
            $proposalId = $this->db->lastInsertId();

            // 2. Insert into proposal_audiences pivot table
            $stmt = $this->db->prepare("INSERT INTO proposal_audiences (proposal_id, audience_id) VALUES (:proposal_id, :audience_id)");
            foreach ($data['audiences'] as $audienceId) {
                $stmt->execute([':proposal_id' => $proposalId, ':audience_id' => $audienceId]);
            }

            // 3. Insert into proposal_organizers pivot table
            $stmt = $this->db->prepare("INSERT INTO proposal_organizers (proposal_id, organizer_id) VALUES (:proposal_id, :organizer_id)");
            foreach ($data['organizers'] as $organizerId) {
                $stmt->execute([':proposal_id' => $proposalId, ':organizer_id' => $organizerId]);
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            // In a real app, log the error message: $e->getMessage()
            return false;
        }
    }

    /**
     * Gets all approved proposals within a specific date range.
     * @param string $startDate 'Y-m-d H:i:s'
     * @param string $endDate 'Y-m-d H:i:s'
     * @return array
     */
    public function getApprovedByWeek($startDate, $endDate) {
        try {
            $stmt = $this->db->prepare(
                "SELECT p.*, u.full_name as author_name
                 FROM proposals p JOIN users u ON p.user_id = u.id
                 WHERE p.status = 'approved' AND p.event_datetime BETWEEN :start_date AND :end_date
                 ORDER BY p.event_datetime ASC"
            );
            $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
            $proposals = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Enhance with audiences and organizers
            foreach ($proposals as $proposal) {
                // Audiences
                $stmt_a = $this->db->prepare("
                    SELECT a.name FROM audiences a
                    JOIN proposal_audiences pa ON a.id = pa.audience_id
                    WHERE pa.proposal_id = :proposal_id
                ");
                $stmt_a->execute([':proposal_id' => $proposal->id]);
                $proposal->audiences = $stmt_a->fetchAll(PDO::FETCH_COLUMN);

                // Organizers
                $stmt_o = $this->db->prepare("
                    SELECT o.name FROM organizers o
                    JOIN proposal_organizers po ON o.id = po.organizer_id
                    WHERE po.proposal_id = :proposal_id
                ");
                $stmt_o->execute([':proposal_id' => $proposal->id]);
                $proposal->organizers = $stmt_o->fetchAll(PDO::FETCH_COLUMN);
            }

            return $proposals;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Gets all pending proposals for a specific user within a date range.
     * @param int $userId
     * @param string $startDate 'Y-m-d H:i:s'
     * @param string $endDate 'Y-m-d H:i:s'
     * @return array
     */
    public function getPendingForUserByWeek($userId, $startDate, $endDate) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM proposals WHERE user_id = :user_id AND status = 'pending' AND event_datetime BETWEEN :start_date AND :end_date ORDER BY event_datetime ASC"
            );
            $stmt->execute([':user_id' => $userId, ':start_date' => $startDate, ':end_date' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPendingByWeek($startDate, $endDate) {
        try {
            $stmt = $this->db->prepare(
                "SELECT p.*, u.full_name as author_name FROM proposals p
                 JOIN users u ON p.user_id = u.id
                 WHERE p.status = 'pending' AND p.event_datetime BETWEEN :start_date AND :end_date
                 ORDER BY p.priority ASC, p.created_at ASC"
            );
            $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function approve($proposalId) {
        try {
            $stmt = $this->db->prepare("UPDATE proposals SET status = 'approved' WHERE id = :id");
            return $stmt->execute([':id' => $proposalId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function revoke($proposalId) {
        try {
            $stmt = $this->db->prepare("UPDATE proposals SET status = 'pending' WHERE id = :id");
            return $stmt->execute([':id' => $proposalId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getProposalDetails($proposalId) {
        // This method should fetch the proposal and its many-to-many relationships
        // This is a complex query, so I will build it carefully.
        // For now, a placeholder returning the main proposal data.
        // The full implementation will join with audiences and organizers.
        try {
            $stmt = $this->db->prepare("SELECT * FROM proposals WHERE id = :id");
            $stmt->execute([':id' => $proposalId]);
            $proposal = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$proposal) return false;

            // Fetch audiences
            $stmt = $this->db->prepare("SELECT audience_id FROM proposal_audiences WHERE proposal_id = :id");
            $stmt->execute([':id' => $proposalId]);
            $proposal->audiences = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Fetch organizers
            $stmt = $this->db->prepare("SELECT organizer_id FROM proposal_organizers WHERE proposal_id = :id");
            $stmt->execute([':id' => $proposalId]);
            $proposal->organizers = $stmt->fetchAll(PDO::FETCH_COLUMN);

            return $proposal;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM proposals WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $this->db->beginTransaction();

            // 1. Update proposals table
            $stmt = $this->db->prepare(
                "UPDATE proposals SET title = :title, event_datetime = :event_datetime, event_end_datetime = :event_end_datetime, objective = :objective, priority = :priority
                 WHERE id = :id"
            );
            $stmt->execute([
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':event_datetime' => $data['event_datetime'],
                ':event_end_datetime' => $data['event_end_datetime'],
                ':objective' => $data['objective'],
                ':priority' => $data['priority']
            ]);

            // 2. Delete old pivot entries
            $this->db->prepare("DELETE FROM proposal_audiences WHERE proposal_id = :id")->execute([':id' => $data['id']]);
            $this->db->prepare("DELETE FROM proposal_organizers WHERE proposal_id = :id")->execute([':id' => $data['id']]);

            // 3. Insert new audiences
            $stmt_a = $this->db->prepare("INSERT INTO proposal_audiences (proposal_id, audience_id) VALUES (:proposal_id, :audience_id)");
            foreach ($data['audiences'] as $audienceId) {
                $stmt_a->execute([':proposal_id' => $data['id'], ':audience_id' => $audienceId]);
            }

            // 4. Insert new organizers
            $stmt_o = $this->db->prepare("INSERT INTO proposal_organizers (proposal_id, organizer_id) VALUES (:proposal_id, :organizer_id)");
            foreach ($data['organizers'] as $organizerId) {
                $stmt_o->execute([':proposal_id' => $data['id'], ':organizer_id' => $organizerId]);
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        try {
            // Transaction to ensure all related data is deleted.
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM proposal_audiences WHERE proposal_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM proposal_organizers WHERE proposal_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM proposals WHERE id = :id")->execute([':id' => $id]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAllProposals() {
        try {
            $stmt = $this->db->query(
                "SELECT p.*, u.full_name as author_name FROM proposals p
                 JOIN users u ON p.user_id = u.id
                 ORDER BY p.event_datetime DESC"
            );
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return [];
        }
    }
}
