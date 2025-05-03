<?php
class SmsController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Redirection de l'ancienne URL vers la nouvelle
    public function preferences() {
        // Rediriger vers l'action settings
        header('Location: index.php?controller=sms&action=settings');
        exit;
    }
    
    // Afficher les paramètres SMS
    public function settings() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les préférences SMS de l'utilisateur
        $preference = new UserSmsPreference($this->db);
        $preference->readByUser($_SESSION['user_id']);
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/sms/settings.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $preference->visit_reminder = isset($_POST['visit_reminder']) ? 1 : 0;
            $preference->visit_confirmation = isset($_POST['visit_confirmation']) ? 1 : 0;
            $preference->visit_cancellation = isset($_POST['visit_cancellation']) ? 1 : 0;
            $preference->daily_summary = isset($_POST['daily_summary']) ? 1 : 0;
            $preference->team_updates = isset($_POST['team_updates']) ? 1 : 0;
            $preference->reminder_time = isset($_POST['reminder_time']) ? intval($_POST['reminder_time']) : 60;
            $preference->active = isset($_POST['active']) ? 1 : 0;
            
            // Mettre à jour les préférences
            if ($preference->createOrUpdate()) {
                setFlashMessage('success', 'Vos préférences de notification SMS ont été mises à jour avec succès.');
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de la mise à jour de vos préférences.');
            }
        }
        
        // Récupérer l'historique des SMS
        $sms = new SmsNotification($this->db);
        $sms_history = $sms->readByUser($_SESSION['user_id'], 10);
        
        // Afficher la vue
        include 'views/sms/settings.php';
    }
    
    // Tester l'envoi d'un SMS
    public function testSms() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer l'utilisateur
        $user = new User($this->db);
        $user->id = $_SESSION['user_id'];
        $user->readOne();
        
        if (empty($user->phone)) {
            setFlashMessage('danger', 'Vous devez d\'abord configurer votre numéro de téléphone dans votre profil.');
            header('Location: index.php?controller=sms&action=settings');
            exit;
        }
        
        // Créer et envoyer un SMS de test
        $sms = new SmsNotification($this->db);
        $sms->user_id = $_SESSION['user_id'];
        $sms->phone = $user->phone;
        $sms->message = "Ceci est un SMS de test de FieldSync. Si vous recevez ce message, vos notifications SMS sont correctement configurées.";
        $sms->status = 'pending';
        $sms->type = 'test';
        $sms->related_id = 0;
        
        if ($sms->create()) {
            // Tenter d'envoyer le SMS immédiatement
            if ($sms->send()) {
                setFlashMessage('success', 'Un SMS de test a été envoyé à votre numéro de téléphone.');
            } else {
                setFlashMessage('warning', 'Le SMS de test a été créé mais n\'a pas pu être envoyé immédiatement. Il sera traité ultérieurement.');
            }
        } else {
            setFlashMessage('danger', 'Une erreur est survenue lors de la création du SMS de test.');
        }
        
        header('Location: index.php?controller=sms&action=settings');
        exit;
    }
    
    // Traiter les SMS en attente (à exécuter via une tâche cron)
    public function processPendingSms() {
        // Cette méthode devrait être protégée par une clé API ou exécutée uniquement en CLI
        // Pour simplifier, nous vérifions juste si la requête vient du serveur local
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) && !isset($_GET['api_key'])) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Accès refusé';
            exit;
        }
        
        // Si une clé API est fournie, la vérifier
        if (isset($_GET['api_key']) && $_GET['api_key'] !== 'votre_clé_api_secrète') {
            header('HTTP/1.1 403 Forbidden');
            echo 'Clé API invalide';
            exit;
        }
        
        // Récupérer les SMS en attente
        $sms = new SmsNotification($this->db);
        $pending_sms = $sms->readPending();
        
        $processed = 0;
        $success = 0;
        
        while ($row = $pending_sms->fetch(PDO::FETCH_ASSOC)) {
            $processed++;
            
            $sms->id = $row['id'];
            $sms->phone = $row['phone'];
            $sms->message = $row['message'];
            
            if ($sms->send()) {
                $success++;
            }
        }
        
        // Retourner un résultat JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'processed' => $processed,
            'success' => $success,
            'failed' => $processed - $success
        ]);
        exit;
    }
    
    // Envoyer des rappels SMS pour les visites à venir
    public function sendVisitReminders() {
        // Cette méthode devrait être protégée comme processPendingSms
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) && !isset($_GET['api_key'])) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Accès refusé';
            exit;
        }
        
        // Si une clé API est fournie, la vérifier
        if (isset($_GET['api_key']) && $_GET['api_key'] !== 'votre_clé_api_secrète') {
            header('HTTP/1.1 403 Forbidden');
            echo 'Clé API invalide';
            exit;
        }
        
        // Récupérer les visites à venir dans les prochaines heures
        $visit = new Visit($this->db);
        $query = "SELECT v.*, vp.user_id, u.name, u.phone 
                  FROM visits v
                  JOIN visit_participants vp ON v.id = vp.visit_id
                  JOIN users u ON vp.user_id = u.id
                  WHERE v.date = CURDATE() 
                  AND v.status = 'confirmed'
                  AND TIME_TO_SEC(TIMEDIFF(CONCAT(v.date, ' ', v.start_time), NOW())) / 60 BETWEEN 30 AND 120";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $sent = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Vérifier les préférences SMS de l'utilisateur
            $preference = new UserSmsPreference($this->db);
            $preference->readByUser($row['user_id']);
            
            if ($preference->shouldReceiveSms('visit_reminder')) {
                // Calculer le temps restant avant la visite
                $visit_datetime = strtotime($row['date'] . ' ' . $row['start_time']);
                $minutes_remaining = round(($visit_datetime - time()) / 60);
                
                // Vérifier si c'est le bon moment pour envoyer le rappel
                if ($minutes_remaining <= $preference->reminder_time && $minutes_remaining > 0) {
                    // Créer le message SMS
                    $message = "Rappel: Vous avez une visite \"" . $row['title'] . "\" aujourd'hui à " . 
                               $row['start_time'] . " à " . $row['location'] . ". ";
                    
                    if (!empty($row['description'])) {
                        $message .= "Détails: " . substr($row['description'], 0, 50);
                        if (strlen($row['description']) > 50) {
                            $message .= "...";
                        }
                    }
                    
                    // Créer et envoyer le SMS
                    $sms = new SmsNotification($this->db);
                    $sms->user_id = $row['user_id'];
                    $sms->phone = $row['phone'];
                    $sms->message = $message;
                    $sms->status = 'pending';
                    $sms->type = 'visit_reminder';
                    $sms->related_id = $row['id'];
                    
                    if ($sms->create() && $sms->send()) {
                        $sent++;
                    }
                }
            }
        }
        
        // Retourner un résultat JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'sent' => $sent
        ]);
        exit;
    }
    
    // Envoyer un résumé quotidien des visites
    public function sendDailySummary() {
        // Protection similaire aux autres méthodes API
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) && !isset($_GET['api_key'])) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Accès refusé';
            exit;
        }
        
        // Récupérer tous les utilisateurs avec des préférences de résumé quotidien activées
        $query = "SELECT u.id, u.name, u.phone 
                  FROM users u
                  JOIN user_sms_preferences p ON u.id = p.user_id
                  WHERE p.daily_summary = 1 AND p.active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $sent = 0;
        
        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Récupérer les visites du jour pour cet utilisateur
            $query = "SELECT v.* 
                      FROM visits v
                      JOIN visit_participants vp ON v.id = vp.visit_id
                      WHERE vp.user_id = :user_id 
                      AND v.date = CURDATE()
                      ORDER BY v.start_time ASC";
            
            $visits_stmt = $this->db->prepare($query);
            $visits_stmt->bindParam(':user_id', $user['id']);
            $visits_stmt->execute();
            
            $visits_count = $visits_stmt->rowCount();
            
            if ($visits_count > 0) {
                // Créer le message de résumé
                $message = "FieldSync - Résumé du jour: Vous avez " . $visits_count . " visite(s) aujourd'hui:\n";
                
                while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
                    $message .= "- " . $visit['start_time'] . ": " . $visit['title'] . " à " . $visit['location'] . "\n";
                }
                
                // Créer et envoyer le SMS
                $sms = new SmsNotification($this->db);
                $sms->user_id = $user['id'];
                $sms->phone = $user['phone'];
                $sms->message = $message;
                $sms->status = 'pending';
                $sms->type = 'daily_summary';
                $sms->related_id = 0;
                
                if ($sms->create() && $sms->send()) {
                    $sent++;
                }
            }
        }
        
        // Retourner un résultat JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'sent' => $sent
        ]);
        exit;
    }
}
?>