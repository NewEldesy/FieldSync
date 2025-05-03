<?php
class AuthController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Afficher la page de connexion
    public function login() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/auth/login.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            // Valider les données
            if (empty($email) || empty($password)) {
                setFlashMessage('danger', 'Veuillez remplir tous les champs.');
                include 'views/auth/login.php';
                return;
            }
            
            // Vérifier les identifiants
            $user = new User($this->db);
            $user->email = $email;
            $user->password = $password;
            
            if ($user->login()) {
                // Créer la session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_role'] = $user->role;
                
                // Rediriger vers le tableau de bord
                header('Location: index.php?controller=dashboard&action=index');
                exit;
            } else {
                setFlashMessage('danger', 'Email ou mot de passe incorrect.');
            }
        }
        
        // Afficher la vue
        include 'views/auth/login.php';
    }
    
    // Afficher la page d'inscription
    public function register() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/auth/register.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
            $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
            $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
            
            // Valider les données
            $errors = [];
            
            if (empty($name)) {
                $errors[] = 'Le nom est requis.';
            }
            
            if (empty($email)) {
                $errors[] = 'L\'email est requis.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email n\'est pas valide.';
            }
            
            if (empty($password)) {
                $errors[] = 'Le mot de passe est requis.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            
            if ($password !== $password_confirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
            
            if (empty($phone)) {
                $errors[] = 'Le numéro de téléphone est requis.';
            }
            
            // S'il y a des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    setFlashMessage('danger', $error);
                }
                include 'views/auth/register.php';
                return;
            }
            
            // Créer l'utilisateur
            $user = new User($this->db);
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->role = 'user'; // Par défaut, l'utilisateur a le rôle 'user'
            $user->phone = $phone;
            
            if ($user->create()) {
                setFlashMessage('success', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');
                header('Location: index.php?controller=auth&action=login');
                exit;
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de la création de votre compte.');
            }
        }
        
        // Afficher la vue
        include 'views/auth/register.php';
    }
    
    // Déconnexion
    public function logout() {
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    
    // Afficher la page de profil
    public function profile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Récupérer les informations de l'utilisateur
        $user = new User($this->db);
        $user->id = $_SESSION['user_id'];
        $user->readOne();
        
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier le token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité. Veuillez réessayer.');
                include 'views/auth/profile.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
            $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
            
            // Valider les données
            $errors = [];
            
            if (empty($name)) {
                $errors[] = 'Le nom est requis.';
            }
            
            if (empty($email)) {
                $errors[] = 'L\'email est requis.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email n\'est pas valide.';
            }
            
            if (empty($phone)) {
                $errors[] = 'Le numéro de téléphone est requis.';
            }
            
            // S'il y a des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    setFlashMessage('danger', $error);
                }
                include 'views/auth/profile.php';
                return;
            }
            
            // Mettre à jour l'utilisateur
            $user->name = $name;
            $user->email = $email;
            $user->phone = $phone;
            
            if ($user->update()) {
                // Mettre à jour le nom dans la session
                $_SESSION['user_name'] = $name;
                
                setFlashMessage('success', 'Votre profil a été mis à jour avec succès.');
            } else {
                setFlashMessage('danger', 'Une erreur est survenue lors de la mise à jour de votre profil.');
            }
        }
        
        // Afficher la vue
        include 'views/auth/profile.php';
    }
    
    // Changer le mot de passe
    public function changePassword() {
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
                include 'views/auth/change_password.php';
                return;
            }
            
            // Récupérer les données du formulaire
            $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Valider les données
            $errors = [];
            
            if (empty($current_password)) {
                $errors[] = 'Le mot de passe actuel est requis.';
            }
            
            if (empty($new_password)) {
                $errors[] = 'Le nouveau mot de passe est requis.';
            } elseif (strlen($new_password) < 6) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
            }
            
            if ($new_password !== $confirm_password) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
            
            // S'il y a des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    setFlashMessage('danger', $error);
                }
                include 'views/auth/change_password.php';
                return;
            }
            
            // Vérifier le mot de passe actuel
            $user = new User($this->db);
            $user->id = $_SESSION['user_id'];
            $user->readOne();
            
            if (password_verify($current_password, $user->password)) {
                // Mettre à jour le mot de passe
                $user->password = $new_password;
                
                if ($user->updatePassword()) {
                    setFlashMessage('success', 'Votre mot de passe a été mis à jour avec succès.');
                    header('Location: index.php?controller=auth&action=profile');
                    exit;
                } else {
                    setFlashMessage('danger', 'Une erreur est survenue lors de la mise à jour de votre mot de passe.');
                }
            } else {
                setFlashMessage('danger', 'Le mot de passe actuel est incorrect.');
            }
        }
        
        // Afficher la vue
        include 'views/auth/change_password.php';
    }
}
?>
