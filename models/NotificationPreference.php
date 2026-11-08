<?php
class NotificationPreference {
    private $conn;
    private $table_name = "notification_preferences";

    public $id;
    public $user_id;
    public $email_daily      = 1;
    public $email_visits     = 1;
    public $app_notifications = 1;
    public $team_changes     = 1;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByUser($user_id) {
        $query = "SELECT * FROM {$this->table_name} WHERE user_id = :user_id LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id               = $row['id'];
            $this->user_id          = $row['user_id'];
            $this->email_daily      = (int) $row['email_daily'];
            $this->email_visits     = (int) $row['email_visits'];
            $this->app_notifications = (int) $row['app_notifications'];
            $this->team_changes     = (int) $row['team_changes'];
            return true;
        }
        $this->user_id = $user_id;
        return false;
    }

    public function createOrUpdate() {
        $query = "INSERT INTO {$this->table_name}
                    (user_id, email_daily, email_visits, app_notifications, team_changes)
                  VALUES
                    (:user_id, :email_daily, :email_visits, :app_notifications, :team_changes)
                  ON DUPLICATE KEY UPDATE
                    email_daily       = VALUES(email_daily),
                    email_visits      = VALUES(email_visits),
                    app_notifications = VALUES(app_notifications),
                    team_changes      = VALUES(team_changes),
                    updated_at        = NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id',          $this->user_id,          PDO::PARAM_INT);
        $stmt->bindParam(':email_daily',      $this->email_daily,      PDO::PARAM_INT);
        $stmt->bindParam(':email_visits',     $this->email_visits,     PDO::PARAM_INT);
        $stmt->bindParam(':app_notifications', $this->app_notifications, PDO::PARAM_INT);
        $stmt->bindParam(':team_changes',     $this->team_changes,     PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function wantsEmail($type) {
        switch ($type) {
            case 'visits':     return (bool) $this->email_visits;
            case 'daily':      return (bool) $this->email_daily;
            case 'team':       return (bool) $this->team_changes;
            default:           return true;
        }
    }
}
?>
