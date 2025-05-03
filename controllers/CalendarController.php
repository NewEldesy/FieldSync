<?php
class CalendarController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Afficher le calendrier
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer le mois et l'année actuels ou ceux spécifiés dans l'URL
        $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        
        // Valider le mois et l'année
        if ($month < 1 || $month > 12) {
            $month = date('n');
        }
        if ($year < 2000 || $year > 2100) {
            $year = date('Y');
        }
        
        // Récupérer les visites du mois
        $visit = new Visit($this->db);
        $visits_stmt = $visit->readAll();
        $visits = [];
        
        while ($row = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
            $visit_date = $row['date'];
            $visits[$visit_date][] = $row;
        }
        
        // Récupérer les disponibilités de l'utilisateur pour le mois
        $availability = new Availability($this->db);
        $availabilities_stmt = $availability->readByUser($_SESSION['user_id']);
        $availabilities = [];
        
        while ($row = $availabilities_stmt->fetch(PDO::FETCH_ASSOC)) {
            $availability_date = $row['date'];
            $availabilities[$availability_date][] = $row;
        }
        
        // Récupérer les membres de l'équipe
        $user = new User($this->db);
        $team_members_stmt = $user->readAll();
        $team_members = [];
        
        while ($row = $team_members_stmt->fetch(PDO::FETCH_ASSOC)) {
            $team_members[] = $row;
        }
        
        // Afficher la vue
        include 'views/calendar/index.php';
    }
    
    // Afficher les détails d'une date
    public function date() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer la date
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        // Valider la date
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        
        // Récupérer les visites de la date
        $visit = new Visit($this->db);
        $visits_stmt = $visit->readByDate($date);
        
        // Récupérer les disponibilités de tous les utilisateurs pour cette date
        $availability = new Availability($this->db);
        $availabilities_stmt = $availability->readByDate($date);
        
        // Récupérer les membres de l'équipe
        $user = new User($this->db);
        $team_members_stmt = $user->readAll();
        $team_members = [];
        
        while ($row = $team_members_stmt->fetch(PDO::FETCH_ASSOC)) {
            $team_members[$row['id']] = $row;
        }
        
        // Afficher la vue
        include 'views/calendar/date.php';
    }
    
    // Gérer les disponibilités
    public function availability() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                header('Location: index.php?controller=calendar&action=index');
                exit;
            }
            
            // Récupérer les données du formulaire
            $date = isset($_POST['date']) ? sanitizeInput($_POST['date']) : '';
            $start_time = isset($_POST['start_time']) ? sanitizeInput($_POST['start_time']) : '';
            $end_time = isset($_POST['end_time']) ? sanitizeInput($_POST['end_time']) : '';
            $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : 'available';
            
            // Valider les données
            if (empty($date) || empty($start_time) || empty($end_time)) {
                setFlashMessage('danger', 'Veuillez remplir tous les champs obligatoires.');
                header('Location: index.php?controller=calendar&action=index');
                exit;
            }
            
            // Créer ou mettre à jour la disponibilité
            $availability = new Availability($this->db);
            $availability->user_id = $_SESSION['user_id'];
            $availability->date = $date;
            $availability->start_time = $start_time;
            $availability->end_time = $end_time;
            $availability->status = $status;
            
            if ($availability->create()) {
                setFlashMessage('success', 'Votre disponibilité a été enregistrée avec succès.');
                
                // Notifier les autres membres de l'équipe
                $user = new User($this->db);
                $user->id = $_SESSION['user_id'];
                $user->readOne();
                
                $notification = new Notification($this->db);
                $notification->title = "Mise à jour de disponibilité";
                $notification->message = $user->name . " a mis à jour sa disponibilité pour le " . formatDateFr($date);
                $notification->type = "availability";
                $notification->related_id = $availability->id;
                
                // Récupérer tous les utilisateurs sauf l'utilisateur actuel
                $team_members_stmt = $user->readAll();
                while ($row = $team_members_stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['id'] != $_SESSION['user_id']) {
                        $notification->user_id = $row['id'];
                        $notification->create();
                    }
                }
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de l\'enregistrement de votre disponibilité.');
            }
            
            // Rediriger vers le calendrier
            header('Location: index.php?controller=calendar&action=index');
            exit;
        }
        
        // Afficher le formulaire
        include 'views/calendar/availability_form.php';
    }
}
?>
