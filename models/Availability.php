<?php
class Availability {
    private $conn;
    private $table_name = "availabilities";
    
    // Propriétés
    public $id;
    public $user_id;
    public $date;
    public $start_time;
    public $end_time;
    public $status;
    public $created_at;
    public $updated_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer une nouvelle disponibilité
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, 
                      date = :date, 
                      start_time = :start_time, 
                      end_time = :end_time, 
                      status = :status, 
                      created_at = NOW(), 
                      updated_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Lier les paramètres
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":status", $this->status);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Lire une disponibilité
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier l'ID
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Récupérer la ligne
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Assigner les valeurs
            $this->user_id = $row['user_id'];
            $this->date = $row['date'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Récupérer les disponibilités d'un utilisateur
    public function readByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY date ASC, start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer les disponibilités par date
    public function readByDate($date) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM " . $this->table_name . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  WHERE a.date = :date
                  ORDER BY a.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer les disponibilités d'un utilisateur par date
    public function readByUserAndDate($user_id, $date) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND date = :date
                  ORDER BY start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":date", $date);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Mettre à jour une disponibilité
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET date = :date, 
                      start_time = :start_time, 
                      end_time = :end_time, 
                      status = :status, 
                      updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Lier les paramètres
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Supprimer une disponibilité
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier l'ID
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Vérifier les disponibilités communes entre utilisateurs
    public function checkCommonAvailability($user_ids, $date) {
        $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
        
        $query = "SELECT a.start_time, a.end_time, COUNT(DISTINCT a.user_id) as user_count
                  FROM " . $this->table_name . " a
                  WHERE a.user_id IN ($placeholders)
                  AND a.date = ?
                  GROUP BY a.start_time, a.end_time
                  HAVING user_count = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres
        $i = 1;
        foreach ($user_ids as $id) {
            $stmt->bindValue($i++, $id);
        }
        $stmt->bindValue($i++, $date);
        $stmt->bindValue($i, count($user_ids));
        
        $stmt->execute();
        
        return $stmt;
    }
}
?>
