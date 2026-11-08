<?php
class Team {
    private $conn;
    private $table_name = "teams";

    // Properties
    public $team_id;
    public $team_name;
    public $description;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create or Update a team
    public function save() {
        if ($this->team_id) {
            // Update
            $query = "UPDATE " . $this->table_name . "
                      SET team_name = :team_name,
                          description = :description,
                          updated_at = NOW()
                      WHERE team_id = :team_id";
        } else {
            // Create
            $query = "INSERT INTO " . $this->table_name . "
                      SET team_name = :team_name,
                          description = :description,
                          created_at = NOW(),
                          updated_at = NOW()";
        }

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->team_name = htmlspecialchars(strip_tags($this->team_name));
        if ($this->description !== null) {
            $this->description = htmlspecialchars(strip_tags($this->description));
        }

        // Bind parameters
        $stmt->bindParam(":team_name", $this->team_name);
        $stmt->bindParam(":description", $this->description);

        if ($this->team_id) {
            $stmt->bindParam(":team_id", $this->team_id);
        }

        if ($stmt->execute()) {
            if (!$this->team_id) {
                $this->team_id = $this->conn->lastInsertId();
            }
            return true;
        }
        return false;
    }

    // Delete a team
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);

        $this->team_id = htmlspecialchars(strip_tags($this->team_id));
        $stmt->bindParam(":team_id", $this->team_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Find a team by ID
    public static function findById($db, $team_id) {
        $query = "SELECT * FROM teams WHERE team_id = :team_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $team = new Team($db);
            $team->team_id = $row['team_id'];
            $team->team_name = $row['team_name'];
            $team->description = $row['description'];
            $team->created_at = $row['created_at'];
            $team->updated_at = $row['updated_at'];
            return $team;
        }
        return null;
    }

    // Find all teams
    public static function findAll($db) {
        $query = "SELECT * FROM teams ORDER BY team_name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $teams = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $team = new Team($db);
                $team->team_id = $row['team_id'];
                $team->team_name = $row['team_name'];
                $team->description = $row['description'];
                $team->created_at = $row['created_at'];
                $team->updated_at = $row['updated_at'];
                $teams[] = $team;
            }
        }
        return $teams; // Should return array of Team objects
    }

    // Get members of this team
    public function getMembers() {
        // Ensure TeamMember class is loaded (e.g. via autoloader or require_once)
        // For now, we assume it's available.
        if (class_exists('TeamMember')) {
            return TeamMember::findByTeamId($this->conn, $this->team_id);
        }
        // Log error or handle missing TeamMember class appropriately
        error_log("TeamMember class not found when calling Team->getMembers()");
        return []; 
    }
}
?>
