<?php
class TeamMember {
    private $conn;
    private $table_name = "team_members";

    // Properties
    public $team_member_id;
    public $team_id;
    public $user_id;
    public $role;
    public $joined_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create or Update a team member record
    public function save() {
        if ($this->team_member_id) {
            // Update - Role is the main thing that might be updated.
            // Re-joining or changing team/user via this method is less common.
            $query = "UPDATE " . $this->table_name . "
                      SET role = :role
                      WHERE team_member_id = :team_member_id";
        } else {
            // Create
            $query = "INSERT INTO " . $this->table_name . "
                      SET team_id = :team_id,
                          user_id = :user_id,
                          role = :role,
                          joined_at = NOW()";
        }

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->role = htmlspecialchars(strip_tags($this->role));
        if (!$this->team_member_id) {
            $this->team_id = htmlspecialchars(strip_tags($this->team_id));
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":user_id", $this->user_id);
        } else {
             $this->team_member_id = htmlspecialchars(strip_tags($this->team_member_id));
             $stmt->bindParam(":team_member_id", $this->team_member_id);
        }
        $stmt->bindParam(":role", $this->role);


        if ($stmt->execute()) {
            if (!$this->team_member_id) {
                $this->team_member_id = $this->conn->lastInsertId();
            }
            return true;
        }
        return false;
    }

    // Delete a team member record
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE team_member_id = :team_member_id";
        $stmt = $this->conn->prepare($query);

        $this->team_member_id = htmlspecialchars(strip_tags($this->team_member_id));
        $stmt->bindParam(":team_member_id", $this->team_member_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Find all members for a given team ID
    public static function findByTeamId($db, $team_id) {
        $query = "SELECT * FROM team_members WHERE team_id = :team_id ORDER BY joined_at ASC";
        $stmt = $db->prepare($query);
        
        $team_id = htmlspecialchars(strip_tags($team_id));
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();

        $members = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $member = new TeamMember($db);
                $member->team_member_id = $row['team_member_id'];
                $member->team_id = $row['team_id'];
                $member->user_id = $row['user_id'];
                $member->role = $row['role'];
                $member->joined_at = $row['joined_at'];
                $members[] = $member;
            }
        }
        return $members;
    }

    // Find all team memberships for a given user ID
    public static function findByUserId($db, $user_id) {
        $query = "SELECT * FROM team_members WHERE user_id = :user_id ORDER BY joined_at ASC";
        $stmt = $db->prepare($query);

        $user_id = htmlspecialchars(strip_tags($user_id));
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $memberships = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $membership = new TeamMember($db);
                $membership->team_member_id = $row['team_member_id'];
                $membership->team_id = $row['team_id'];
                $membership->user_id = $row['user_id'];
                $membership->role = $row['role'];
                $membership->joined_at = $row['joined_at'];
                $memberships[] = $membership;
            }
        }
        return $memberships;
    }

    // Check if a user is part of a team
    public static function isUserMember($db, $team_id, $user_id) {
        $query = "SELECT COUNT(*) as count FROM team_members WHERE team_id = :team_id AND user_id = :user_id";
        $stmt = $db->prepare($query);

        $team_id = htmlspecialchars(strip_tags($team_id));
        $user_id = htmlspecialchars(strip_tags($user_id));

        $stmt->bindParam(":team_id", $team_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['count'] > 0;
    }
}
?>
