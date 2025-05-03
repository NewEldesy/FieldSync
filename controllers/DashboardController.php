<?php
class DashboardController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Afficher le tableau de bord
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les visites à venir
        $visit = new Visit($this->db);
        $upcoming_visits = $visit->readUpcoming();
        
        // Récupérer les membres de l'équipe
        $user = new User($this->db);
        $team_members = $user->readAll();
        
        // Récupérer les notifications non lues
        $notification = new Notification($this->db);
        $unread_notifications = $notification->readUnreadByUser($_SESSION['user_id']);
        
        // Récupérer toutes les notifications
        $all_notifications = $notification->readByUser($_SESSION['user_id'], 5);
        
        // Afficher la vue
        include 'views/dashboard/index.php';
    }
}
?>
