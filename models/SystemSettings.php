<?php
class SystemSettings {
    private $conn;
    private $table_name = "system_settings";

    private static $sensitive_keywords = ['key', 'token', 'password', 'secret', 'sid', 'auth'];

    public function __construct($db) {
        $this->conn = $db;
    }

    public function get($key, $default = null) {
        $query = "SELECT setting_value FROM {$this->table_name} WHERE setting_key = :key LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['setting_value'] : $default;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT setting_key, setting_value, description FROM {$this->table_name} ORDER BY setting_key");
        $stmt->execute();
        return $stmt;
    }

    public function set($key, $value) {
        foreach (self::$sensitive_keywords as $kw) {
            if (stripos($key, $kw) !== false) {
                throw new InvalidArgumentException("La clé '{$key}' contient un terme sensible. Les secrets doivent rester dans le fichier .env.");
            }
        }
        $query = "INSERT INTO {$this->table_name} (setting_key, setting_value, updated_at)
                  VALUES (:key, :value, NOW())
                  ON DUPLICATE KEY UPDATE setting_value = :value2, updated_at = NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key',    $key);
        $stmt->bindParam(':value',  $value);
        $stmt->bindParam(':value2', $value);
        return $stmt->execute();
    }
}
?>
