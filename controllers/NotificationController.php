<?php
class NotificationController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Afficher les notifications
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les notifications
        $notification = new Notification($this->db);
        $notifications_stmt = $notification->readByUser($_SESSION['user_id'], 20);
        
        // Afficher la vue
        include 'views/notifications/index.php';
    }
    
    // Marquer une notification comme lue
    public function markAsRead() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de la notification
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            setFlashMessage('danger', 'ID de notification invalide.');
            header('Location: index.php?controller=notification&action=index');
            exit;
        }
        
        // Marquer la notification comme lue
        $notification = new Notification($this->db);
        $notification->id = $id;
        
        if ($notification->markAsRead()) {
            setFlashMessage('success', 'La notification a été marquée comme lue.');
        } else {
            setFlashMessage('danger', 'Une erreur est survenue.');
        }
        
        // Rediriger vers la page précédente ou la liste des notifications
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?controller=notification&action=index';
        header('Location: ' . $redirect);
        exit;
    }
    
    // Marquer toutes les notifications comme lues
    public function markAllAsRead() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Marquer toutes les notifications comme lues
        $notification = new Notification($this->db);
        
        if ($notification->markAllAsRead($_SESSION['user_id'])) {
            setFlashMessage('success', 'Toutes les notifications ont été marquées comme lues.');
        } else {
            setFlashMessage('danger', 'Une erreur est survenue.');
        }
        
        // Rediriger vers la liste des notifications
        header('Location: index.php?controller=notification&action=index');
        exit;
    }
    
    // Paramètres de notification
    public function settings() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les préférences actuelles de l'utilisateur
        // Dans une application réelle, ces données viendraient de la base de données
        // Pour cet exemple, nous utilisons des valeurs par défaut
        $user_prefs = [
            'email_daily' => true,
            'email_visits' => true,
            'sms_reminder' => false,
            'app_notifications' => true,
            'team_changes' => true
        ];
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/notifications/settings.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $user_prefs = [
                'email_daily' => isset($_POST['email_daily']) ? 1 : 0,
                'email_visits' => isset($_POST['email_visits']) ? 1 : 0,
                'sms_reminder' => isset($_POST['sms_reminder']) ? 1 : 0,
                'app_notifications' => isset($_POST['app_notifications']) ? 1 : 0,
                'team_changes' => isset($_POST['team_changes']) ? 1 : 0
            ];
            
            // Mettre à jour les paramètres
            // Dans une application réelle, on sauvegarderait ces préférences dans la base de données
            // Ici, on simule juste une mise à jour réussie
            
            setFlashMessage('success', 'Vos paramètres de notification ont été mis à jour avec succès.');
        }
        
        // Afficher la vue
        include 'views/notifications/settings.php';
    }
}
?>
