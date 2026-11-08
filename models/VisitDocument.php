<?php
class VisitDocument {
    private $conn;
    private $table_name = "visit_documents";

    public $id;
    public $visit_id;
    public $name;
    public $file_path;
    public $file_type;
    public $file_size;
    public $uploaded_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
                  SET visit_id    = :visit_id,
                      name        = :name,
                      file_path   = :file_path,
                      file_type   = :file_type,
                      file_size   = :file_size,
                      uploaded_by = :uploaded_by,
                      created_at  = NOW()";

        $stmt = $this->conn->prepare($query);
        $this->name      = htmlspecialchars(strip_tags($this->name ?? ''));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path ?? ''));
        $this->file_type = htmlspecialchars(strip_tags($this->file_type ?? ''));

        $stmt->bindParam(':visit_id',    $this->visit_id,    PDO::PARAM_INT);
        $stmt->bindParam(':name',        $this->name);
        $stmt->bindParam(':file_path',   $this->file_path);
        $stmt->bindParam(':file_type',   $this->file_type);
        $stmt->bindParam(':file_size',   $this->file_size,   PDO::PARAM_INT);
        $stmt->bindParam(':uploaded_by', $this->uploaded_by, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readByVisit($visit_id) {
        $query = "SELECT vd.*, u.name as uploader_name
                  FROM {$this->table_name} vd
                  LEFT JOIN users u ON vd.uploaded_by = u.id
                  WHERE vd.visit_id = :visit_id
                  ORDER BY vd.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':visit_id', $visit_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
