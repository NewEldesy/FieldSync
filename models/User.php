<?php
class User {
    private $conn;
    private $table_name = "users";
    
    // Propriétés
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $phone;
    public $created_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer un nouvel utilisateur
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name = :name, 
                      email = :email, 
                      password = :password, 
                      role = :role, 
                      phone = :phone, 
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        
        // Lier les paramètres
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":phone", $this->phone);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Vérifier si un utilisateur existe avec l'email et le mot de passe
    public function login() {
        $query = "SELECT id, name, email, password, role, phone 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Lier les paramètres
        $stmt->bindParam(":email", $this->email);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Vérifier si l'utilisateur existe
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($this->password, $row['password'])) {
                // Assigner les valeurs aux propriétés de l'objet
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->role = $row['role'];
                $this->phone = $row['phone'];
                
                return true;
            }
        }
        
        return false;
    }
    
    // Récupérer un utilisateur par son ID
    public function readOne() {
        $query = "SELECT id, name, email, role, phone, created_at 
                  FROM " . $this->table_name . " 
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
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->phone = $row['phone'];
            $this->created_at = $row['created_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Récupérer tous les utilisateurs
    public function readAll() {
        $query = "SELECT id, name, email, role, phone, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Mettre à jour un utilisateur
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, 
                      email = :email, 
                      role = :role, 
                      phone = :phone 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Lier les paramètres
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour le mot de passe
    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Hasher le mot de passe
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Lier les paramètres
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
