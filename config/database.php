<?php
// Configuration de la base de données

class Database {
    private $host = 'localhost';
    private $db_name = 'fieldsync';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    // Obtenir la connexion à la base de données
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
