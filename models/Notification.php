<?php
class Notification {
    private $conn;
    private $table_name = "notifications";
    
    // Propriétés
    public $id;
    public $user_id;
    public $title;
    public $message;
    public $type;
    public $related_id;
    public $is_read;
    public $created_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer une nouvelle notification
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, 
                      title = :title, 
                      message = :message, 
                      type = :type, 
                      related_id = :related_id, 
                      is_read = 0, 
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->related_id = htmlspecialchars(strip_tags($this->related_id));
        
        // Lier les paramètres
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":related_id", $this->related_id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Récupérer les notifications d'un utilisateur
    public function readByUser($user_id, $limit = 10) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer les notifications non lues d'un utilisateur
    public function readUnreadByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND is_read = 0 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Marquer une notification comme lue
    public function markAsRead() {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Marquer toutes les notifications d'un utilisateur comme lues
    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Supprimer une notification
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Envoyer une notification par email
    public function sendEmailNotification($user_email, $user_name) {
        $subject = "FieldSync - " . $this->title;
        
        $body = "<html><body>";
        $body .= "<h2>Bonjour " . $user_name . ",</h2>";
        $body .= "<p>" . $this->message . "</p>";
        $body .= "<p>Connectez-vous à <a href='" . APP_URL . "'>FieldSync</a> pour plus de détails.</p>";
        $body .= "<p>Cordialement,<br>L'équipe FieldSync</p>";
        $body .= "</body></html>";
        
        return sendEmail($user_email, $subject, $body);
    }
    
    // Envoyer une notification par SMS
    public function sendSmsNotification($user_phone) {
        $message = "FieldSync - " . $this->title . ": " . $this->message;
        return sendSMS($user_phone, $message);
    }
}
?>
