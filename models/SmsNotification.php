<?php
class SmsNotification {
    private $conn;
    private $table_name = "sms_notifications";
    
    // Propriétés
    public $id;
    public $user_id;
    public $phone;
    public $message;
    public $status;
    public $type;
    public $related_id;
    public $created_at;
    public $sent_at;
    
    // Constructeur avec connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Créer une nouvelle notification SMS
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, 
                      phone = :phone, 
                      message = :message, 
                      status = :status, 
                      type = :type, 
                      related_id = :related_id, 
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->related_id = htmlspecialchars(strip_tags($this->related_id));
        
        // Lier les paramètres
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":related_id", $this->related_id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour le statut d'une notification SMS
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, 
                      sent_at = " . ($status == 'sent' ? 'NOW()' : 'NULL') . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $this->id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Récupérer les notifications SMS en attente
    public function readPending() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'pending' 
                  ORDER BY created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Récupérer l'historique des SMS pour un utilisateur
    public function readByUser($user_id, $limit = 20) {
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
    
    // Envoyer un SMS via TextBelt (gratuit : 1 SMS/jour avec key='textbelt', payant sinon)
    public function sendViaTextBelt() {
        $api_key = defined('SMS_TEXTBELT_KEY') ? SMS_TEXTBELT_KEY : 'textbelt';

        $data = http_build_query([
            'phone'   => $this->phone,
            'message' => $this->message,
            'key'     => $api_key,
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data,
                'timeout' => 10,
            ]
        ]);

        $result = @file_get_contents('https://textbelt.com/text', false, $context);

        if ($result === false) {
            error_log('TextBelt : impossible de joindre l\'API');
            $this->updateStatus('failed');
            return false;
        }

        $response = json_decode($result, true);

        if (!empty($response['success'])) {
            $this->updateStatus('sent');
            return true;
        }

        $reason = !empty($response['error']) ? $response['error'] : 'unknown';
        error_log('TextBelt erreur : ' . $reason);
        $this->updateStatus('failed');
        return false;
    }

    // Envoyer un SMS via Twilio
    public function sendViaTwilio() {
        if (!defined('SMS_ACCOUNT_SID') || !defined('SMS_AUTH_TOKEN') || empty(SMS_ACCOUNT_SID) || empty(SMS_AUTH_TOKEN)) {
            error_log('Twilio : SMS_ACCOUNT_SID ou SMS_AUTH_TOKEN manquant dans .env');
            $this->updateStatus('failed');
            return false;
        }

        if (!class_exists('Twilio\Rest\Client')) {
            error_log('Twilio : SDK non disponible, vérifiez vendor/autoload.php');
            $this->updateStatus('failed');
            return false;
        }

        try {
            $twilio = new Twilio\Rest\Client(SMS_ACCOUNT_SID, SMS_AUTH_TOKEN);

            $params = ['body' => $this->message];

            // Messaging Service SID prioritaire, sinon numéro From
            if (defined('SMS_MESSAGING_SERVICE_SID') && !empty(SMS_MESSAGING_SERVICE_SID)) {
                $params['messagingServiceSid'] = SMS_MESSAGING_SERVICE_SID;
            } elseif (defined('SMS_FROM_NUMBER') && !empty(SMS_FROM_NUMBER)) {
                $params['from'] = SMS_FROM_NUMBER;
            } else {
                error_log('Twilio : SMS_MESSAGING_SERVICE_SID et SMS_FROM_NUMBER manquants');
                $this->updateStatus('failed');
                return false;
            }

            $message = $twilio->messages->create($this->phone, $params);

            if ($message->sid) {
                $this->updateStatus('sent');
                return true;
            }
        } catch (Exception $e) {
            error_log('Twilio erreur : ' . $e->getMessage());
            $this->updateStatus('failed');
        }

        return false;
    }
    
    // Envoyer un SMS via une autre passerelle (exemple: Vonage/Nexmo)
    public function sendViaNexmo() {
        // Implémentation similaire à Twilio mais avec l'API Nexmo/Vonage
        // Pour cette démonstration, nous simulons l'envoi
        $success = true;
        
        if ($success) {
            $this->updateStatus('sent');
            return true;
        }
        
        $this->updateStatus('failed');
        return false;
    }
    
    // Envoyer un SMS en utilisant le fournisseur configuré
    public function send() {
        // Récupérer le fournisseur SMS configuré
        $sms_provider = defined('SMS_PROVIDER') ? SMS_PROVIDER : 'twilio';
        
        switch ($sms_provider) {
            case 'textbelt':
                return $this->sendViaTextBelt();
            case 'twilio':
                return $this->sendViaTwilio();
            case 'nexmo':
                return $this->sendViaNexmo();
            default:
                return false;
        }
    }
}
?>
