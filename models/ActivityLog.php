<?php
class ActivityLog {
    private $conn;
    private $table_name = "activity_logs";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function log($user_id, $action, $entity_type, $entity_id, $details = null) {
        $query = "INSERT INTO {$this->table_name}
                  SET user_id     = :user_id,
                      action      = :action,
                      entity_type = :entity_type,
                      entity_id   = :entity_id,
                      details     = :details,
                      ip_address  = :ip,
                      created_at  = NOW()";

        $stmt = $this->conn->prepare($query);
        $ip   = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt->bindParam(':user_id',     $user_id,     PDO::PARAM_INT);
        $stmt->bindParam(':action',      $action);
        $stmt->bindParam(':entity_type', $entity_type);
        $stmt->bindParam(':entity_id',   $entity_id,   PDO::PARAM_INT);
        $stmt->bindParam(':details',     $details);
        $stmt->bindParam(':ip',          $ip);

        return $stmt->execute();
    }

    public function readRecent($limit = 50) {
        $query = "SELECT al.*, u.name as user_name
                  FROM {$this->table_name} al
                  LEFT JOIN users u ON al.user_id = u.id
                  ORDER BY al.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readByEntity($entity_type, $entity_id) {
        $query = "SELECT al.*, u.name as user_name
                  FROM {$this->table_name} al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE al.entity_type = :entity_type AND al.entity_id = :entity_id
                  ORDER BY al.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':entity_type', $entity_type);
        $stmt->bindParam(':entity_id',   $entity_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>
