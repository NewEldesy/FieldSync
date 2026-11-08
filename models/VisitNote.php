<?php
class VisitNote {
    private $conn;
    private $table_name = "visit_notes";

    public $id;
    public $visit_id;
    public $user_id;
    public $content;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
                  SET visit_id = :visit_id,
                      user_id  = :user_id,
                      content  = :content,
                      created_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $this->content = htmlspecialchars(strip_tags($this->content ?? ''));
        $stmt->bindParam(':visit_id', $this->visit_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id',  $this->user_id,  PDO::PARAM_INT);
        $stmt->bindParam(':content',  $this->content);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readByVisit($visit_id) {
        $query = "SELECT vn.*, u.name as author_name
                  FROM {$this->table_name} vn
                  LEFT JOIN users u ON vn.user_id = u.id
                  WHERE vn.visit_id = :visit_id
                  ORDER BY vn.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':visit_id', $visit_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id',      $this->id,      PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
