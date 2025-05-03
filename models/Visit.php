<?php
class Visit {
    private $conn;
    private $table_name = "visits";
    
    // Propriétés
    public $id;
    public $title;
    public $description;
    public $location;
    public $date;
    public $start_time;
    public $end_time;
    public $status;
    public $created_by;
    public $created_at;
    public $updated_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer une nouvelle visite
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title = :title, 
                      description = :description, 
                      location = :location, 
                      date = :date, 
                      start_time = :start_time, 
                      end_time = :end_time, 
                      status = :status, 
                      created_by = :created_by, 
                      created_at = NOW(), 
                      updated_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));
        
        // Lier les paramètres
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_by", $this->created_by);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Lire une visite
    public function readOne() {
        $query = "SELECT v.*, u.name as creator_name 
                  FROM " . $this->table_name . " v
                  LEFT JOIN users u ON v.created_by = u.id
                  WHERE v.id = :id 
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
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->location = $row['location'];
            $this->date = $row['date'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->status = $row['status'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Récupérer toutes les visites
    public function readAll() {
        $query = "SELECT v.*, u.name as creator_name 
                  FROM " . $this->table_name . " v
                  LEFT JOIN users u ON v.created_by = u.id
                  ORDER BY v.date ASC, v.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer les visites par date
    public function readByDate($date) {
        $query = "SELECT v.*, u.name as creator_name 
                  FROM " . $this->table_name . " v
                  LEFT JOIN users u ON v.created_by = u.id
                  WHERE v.date = :date
                  ORDER BY v.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer les visites à venir
    public function readUpcoming() {
        $query = "SELECT v.*, u.name as creator_name 
                  FROM " . $this->table_name . " v
                  LEFT JOIN users u ON v.created_by = u.id
                  WHERE v.date >= CURDATE()
                  ORDER BY v.date ASC, v.start_time ASC
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Mettre à jour une visite
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, 
                      description = :description, 
                      location = :location, 
                      date = :date, 
                      start_time = :start_time, 
                      end_time = :end_time, 
                      status = :status, 
                      updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Lier les paramètres
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":location", $this->location);
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
    
    // Supprimer une visite
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
    
    // Ajouter un participant à une visite
    public function addParticipant($user_id) {
        // Vérifier d'abord si le participant existe déjà
        $check_query = "SELECT COUNT(*) FROM visit_participants 
                        WHERE visit_id = :visit_id AND user_id = :user_id";
        
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":visit_id", $this->id);
        $check_stmt->bindParam(":user_id", $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->fetchColumn() > 0) {
            // Le participant existe déjà, on peut retourner true car il est déjà associé
            return true;
        }
        
        // Le participant n'existe pas, on l'ajoute
        $query = "INSERT INTO visit_participants 
                  SET visit_id = :visit_id, 
                      user_id = :user_id, 
                      status = 'pending', 
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres
        $stmt->bindParam(":visit_id", $this->id);
        $stmt->bindParam(":user_id", $user_id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Récupérer les participants d'une visite
    public function getParticipants() {
        $query = "SELECT vp.*, u.name, u.email, u.phone 
                  FROM visit_participants vp
                  LEFT JOIN users u ON vp.user_id = u.id
                  WHERE vp.visit_id = :visit_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":visit_id", $this->id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Mettre à jour le statut d'un participant
    public function updateParticipantStatus($user_id, $status) {
        $query = "UPDATE visit_participants 
                  SET status = :status 
                  WHERE visit_id = :visit_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":visit_id", $this->id);
        $stmt->bindParam(":user_id", $user_id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
