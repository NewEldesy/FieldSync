<?php
class UserSmsPreference {
    private $conn;
    private $table_name = "user_sms_preferences";
    
    // Propriétés
    public $id;
    public $user_id;
    public $visit_reminder;
    public $visit_confirmation;
    public $visit_cancellation;
    public $daily_summary;
    public $team_updates;
    public $reminder_time; // Minutes avant l'événement
    public $active;
    public $created_at;
    public $updated_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer ou mettre à jour les préférences SMS d'un utilisateur
    public function createOrUpdate() {
        // Vérifier si l'utilisateur a déjà des préférences
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Mettre à jour les préférences existantes
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            
            $query = "UPDATE " . $this->table_name . " 
                      SET visit_reminder = :visit_reminder, 
                          visit_confirmation = :visit_confirmation, 
                          visit_cancellation = :visit_cancellation, 
                          daily_summary = :daily_summary, 
                          team_updates = :team_updates, 
                          reminder_time = :reminder_time, 
                          active = :active, 
                          updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
        } else {
            // Créer de nouvelles préférences
            $query = "INSERT INTO " . $this->table_name . " 
                      SET user_id = :user_id, 
                          visit_reminder = :visit_reminder, 
                          visit_confirmation = :visit_confirmation, 
                          visit_cancellation = :visit_cancellation, 
                          daily_summary = :daily_summary, 
                          team_updates = :team_updates, 
                          reminder_time = :reminder_time, 
                          active = :active, 
                          created_at = NOW(), 
                          updated_at = NOW()";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
        }
        
        // Nettoyer et lier les données
        $this->visit_reminder = $this->visit_reminder ? 1 : 0;
        $this->visit_confirmation = $this->visit_confirmation ? 1 : 0;
        $this->visit_cancellation = $this->visit_cancellation ? 1 : 0;
        $this->daily_summary = $this->daily_summary ? 1 : 0;
        $this->team_updates = $this->team_updates ? 1 : 0;
        $this->active = $this->active ? 1 : 0;
        $this->reminder_time = intval($this->reminder_time);
        
        $stmt->bindParam(":visit_reminder", $this->visit_reminder);
        $stmt->bindParam(":visit_confirmation", $this->visit_confirmation);
        $stmt->bindParam(":visit_cancellation", $this->visit_cancellation);
        $stmt->bindParam(":daily_summary", $this->daily_summary);
        $stmt->bindParam(":team_updates", $this->team_updates);
        $stmt->bindParam(":reminder_time", $this->reminder_time);
        $stmt->bindParam(":active", $this->active);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            if (!$this->id) {
                $this->id = $this->conn->lastInsertId();
            }
            return true;
        }
        
        return false;
    }
    
    // Récupérer les préférences SMS d'un utilisateur
    public function readByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->visit_reminder = $row['visit_reminder'];
            $this->visit_confirmation = $row['visit_confirmation'];
            $this->visit_cancellation = $row['visit_cancellation'];
            $this->daily_summary = $row['daily_summary'];
            $this->team_updates = $row['team_updates'];
            $this->reminder_time = $row['reminder_time'];
            $this->active = $row['active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        // Si aucune préférence n'existe, définir des valeurs par défaut
        $this->user_id = $user_id;
        $this->visit_reminder = 1;
        $this->visit_confirmation = 1;
        $this->visit_cancellation = 1;
        $this->daily_summary = 1;
        $this->team_updates = 0;
        $this->reminder_time = 60; // 60 minutes par défaut
        $this->active = 1;
        
        return false;
    }
    
    // Vérifier si un utilisateur doit recevoir un type spécifique de notification SMS
    public function shouldReceiveSms($type) {
        if (!$this->active) {
            return false;
        }
        
        switch ($type) {
            case 'visit_reminder':
                return $this->visit_reminder == 1;
            case 'visit_confirmation':
                return $this->visit_confirmation == 1;
            case 'visit_cancellation':
                return $this->visit_cancellation == 1;
            case 'daily_summary':
                return $this->daily_summary == 1;
            case 'team_updates':
                return $this->team_updates == 1;
            default:
                return false;
        }
    }
}
?>
