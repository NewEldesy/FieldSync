<?php
class VisitController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Afficher la liste des visites
    public function index() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les visites
        $visit = new Visit($this->db);
        $visits_stmt = $visit->readAll();
        
        // Afficher la vue
        include 'views/visits/index.php';
    }
    
    // Afficher les détails d'une visite
    public function view() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de la visite
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            setFlashMessage('danger', 'ID de visite invalide.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Récupérer la visite
        $visit = new Visit($this->db);
        $visit->id = $id;
        
        if (!$visit->readOne()) {
            setFlashMessage('danger', 'Visite non trouvée.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Récupérer les participants
        $participants_stmt = $visit->getParticipants();
        
        // Afficher la vue
        include 'views/visits/view.php';
    }
    
    // Afficher le formulaire de création de visite
    public function create() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les membres de l'équipe
        $user = new User($this->db);
        $team_members_stmt = $user->readAll();
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/visits/create.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $title = isset($_POST['title']) ? sanitizeInput($_POST['title']) : '';
            $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
            $location = isset($_POST['location']) ? sanitizeInput($_POST['location']) : '';
            $date = isset($_POST['date']) ? sanitizeInput($_POST['date']) : '';
            $start_time = isset($_POST['start_time']) ? sanitizeInput($_POST['start_time']) : '';
            $end_time = isset($_POST['end_time']) ? sanitizeInput($_POST['end_time']) : '';
            $participants = isset($_POST['participants']) ? $_POST['participants'] : [];
            
            // Valider les données
            $errors = [];
            
            if (empty($title)) {
                $errors[] = 'Le titre est requis.';
            }
            
            if (empty($location)) {
                $errors[] = 'Le lieu est requis.';
            }
            
            if (empty($date)) {
                $errors[] = 'La date est requise.';
            }
            
            if (empty($start_time)) {
                $errors[] = 'L\'heure de début est requise.';
            }
            
            if (empty($end_time)) {
                $errors[] = 'L\'heure de fin est requise.';
            }
            
            if (empty($participants)) {
                $errors[] = 'Veuillez sélectionner au moins un participant.';
            }
            
            // S'il y a des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    setFlashMessage('danger', $error);
                }
                include 'views/visits/create.php';
                return;
            }
            
            // Créer la visite
            $visit = new Visit($this->db);
            $visit->title = $title;
            $visit->description = $description;
            $visit->location = $location;
            $visit->date = $date;
            $visit->start_time = $start_time;
            $visit->end_time = $end_time;
            $visit->status = 'pending';
            $visit->created_by = $_SESSION['user_id'];
            
            if ($visit->create()) {
                // Ajouter les participants
                foreach ($participants as $participant_id) {
                    $visit->addParticipant($participant_id);
                    
                    // Envoyer une notification à chaque participant
                    $notification = new Notification($this->db);
                    $notification->user_id = $participant_id;
                    $notification->title = "Nouvelle visite";
                    $notification->message = "Vous avez été invité à participer à la visite \"" . $title . "\" le " . formatDateFr($date);
                    $notification->type = "visit";
                    $notification->related_id = $visit->id;
                    $notification->create();
                    
                    // Récupérer les informations du participant pour l'email/SMS
                    $participant = new User($this->db);
                    $participant->id = $participant_id;
                    $participant->readOne();
                    
                    // Envoyer un email
                    $notification->sendEmailNotification($participant->email, $participant->name);
                    
                    // Envoyer un SMS
                    $notification->sendSmsNotification($participant->phone);
                }
                
                setFlashMessage('success', 'La visite a été créée avec succès.');
                header('Location: index.php?controller=visit&action=index');
                exit;
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de la création de la visite.');
            }
        }
        
        // Afficher la vue
        include 'views/visits/create.php';
    }
    
    // Afficher le formulaire de modification de visite
    public function edit() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de la visite
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            setFlashMessage('danger', 'ID de visite invalide.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Récupérer la visite
        $visit = new Visit($this->db);
        $visit->id = $id;
        
        if (!$visit->readOne()) {
            setFlashMessage('danger', 'Visite non trouvée.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Vérifier si l'utilisateur est le créateur de la visite
        if ($visit->created_by != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            setFlashMessage('danger', 'Vous n\'êtes pas autorisé à modifier cette visite.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Récupérer les membres de l'équipe
        $user = new User($this->db);
        $team_members_stmt = $user->readAll();
        
        // Récupérer les participants actuels
        $participants_stmt = $visit->getParticipants();
        $current_participants = [];
        
        while ($row = $participants_stmt->fetch(PDO::FETCH_ASSOC)) {
            $current_participants[] = $row['user_id'];
        }
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/visits/edit.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $title = isset($_POST['title']) ? sanitizeInput($_POST['title']) : '';
            $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
            $location = isset($_POST['location']) ? sanitizeInput($_POST['location']) : '';
            $date = isset($_POST['date']) ? sanitizeInput($_POST['date']) : '';
            $start_time = isset($_POST['start_time']) ? sanitizeInput($_POST['start_time']) : '';
            $end_time = isset($_POST['end_time']) ? sanitizeInput($_POST['end_time']) : '';
            $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
            $participants = isset($_POST['participants']) ? $_POST['participants'] : [];
            
            // Valider les données
            $errors = [];
            
            if (empty($title)) {
                $errors[] = 'Le titre est requis.';
            }
            
            if (empty($location)) {
                $errors[] = 'Le lieu est requis.';
            }
            
            if (empty($date)) {
                $errors[] = 'La date est requise.';
            }
            
            if (empty($start_time)) {
                $errors[] = 'L\'heure de début est requise.';
            }
            
            if (empty($end_time)) {
                $errors[] = 'L\'heure de fin est requise.';
            }
            
            if (empty($participants)) {
                $errors[] = 'Veuillez sélectionner au moins un participant.';
            }
            
            // S'il y a des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    setFlashMessage('danger', $error);
                }
                include 'views/visits/edit.php';
                return;
            }
            
            // Mettre à jour la visite
            $visit->title = $title;
            $visit->description = $description;
            $visit->location = $location;
            $visit->date = $date;
            $visit->start_time = $start_time;
            $visit->end_time = $end_time;
            $visit->status = $status;
            
            if ($visit->update()) {
                // Gérer les participants
                // Approche 1: Supprimer tous les participants actuels et ajouter les nouveaux
                // Cette approche est simple mais peut générer beaucoup de notifications
                
                // Approche 2: Comparer les listes et faire des modifications ciblées
                // Participants à ajouter (nouveaux)
                $participants_to_add = array_diff($participants, $current_participants);
                
                // Ajouter les nouveaux participants
                foreach ($participants_to_add as $participant_id) {
                    $visit->addParticipant($participant_id);
                    
                    // Envoyer une notification au nouveau participant
                    $notification = new Notification($this->db);
                    $notification->user_id = $participant_id;
                    $notification->title = "Invitation à une visite";
                    $notification->message = "Vous avez été invité à participer à la visite \"" . $title . "\" le " . formatDateFr($date);
                    $notification->type = "visit";
                    $notification->related_id = $visit->id;
                    $notification->create();
                    
                    // Récupérer les informations du participant pour l'email/SMS
                    $participant = new User($this->db);
                    $participant->id = $participant_id;
                    $participant->readOne();
                    
                    // Envoyer un email
                    $notification->sendEmailNotification($participant->email, $participant->name);
                    
                    // Envoyer un SMS
                    $notification->sendSmsNotification($participant->phone);
                }
                
                // Participants à supprimer (retirés)
                $participants_to_remove = array_diff($current_participants, $participants);
                
                // Supprimer les participants retirés
                foreach ($participants_to_remove as $participant_id) {
                    // Supprimer le participant
                    $query = "DELETE FROM visit_participants WHERE visit_id = :visit_id AND user_id = :user_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':visit_id', $visit->id);
                    $stmt->bindParam(':user_id', $participant_id);
                    $stmt->execute();
                    
                    // Envoyer une notification au participant retiré
                    $notification = new Notification($this->db);
                    $notification->user_id = $participant_id;
                    $notification->title = "Retrait d'une visite";
                    $notification->message = "Vous avez été retiré de la visite \"" . $title . "\" le " . formatDateFr($date);
                    $notification->type = "visit";
                    $notification->related_id = $visit->id;
                    $notification->create();
                }
                
                setFlashMessage('success', 'La visite a été mise à jour avec succès.');
                header('Location: index.php?controller=visit&action=view&id=' . $id);
                exit;
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de la mise à jour de la visite.');
            }
        }
        
        // Afficher la vue
        include 'views/visits/edit.php';
    }
    
    // Supprimer une visite
    public function delete() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer l'ID de la visite
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            setFlashMessage('danger', 'ID de visite invalide.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Récupérer la visite
        $visit = new Visit($this->db);
        $visit->id = $id;
        
        if (!$visit->readOne()) {
            setFlashMessage('danger', 'Visite non trouvée.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Vérifier si l'utilisateur est le créateur de la visite
        if ($visit->created_by != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            setFlashMessage('danger', 'Vous n\'êtes pas autorisé à supprimer cette visite.');
            header('Location: index.php?controller=visit&action=index');
            exit;
        }
        
        // Supprimer la visite
        if ($visit->delete()) {
            setFlashMessage('success', 'La visite a été supprimée avec succès.');
        } else {
            setFlashMessage('danger', 'Une erreur est survenue lors de la suppression de la visite.');
        }
        
        header('Location: index.php?controller=visit&action=index');
        exit;
    }
}
?>