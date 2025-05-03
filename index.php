<?php
// Point d'entrée principal de l'application
session_start();

// Configuration de base
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

// Autoloader pour les classes
spl_autoload_register(function ($class_name) {
    // Convertir les namespace en chemins de fichiers
    $class_file = str_replace('\\', '/', $class_name) . '.php';
    
    // Chercher dans les dossiers models, controllers, etc.
    $directories = ['models', 'controllers', 'views', 'helpers'];
    
    foreach ($directories as $directory) {
        $file = $directory . '/' . $class_file;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Router simple
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Vérifier si l'utilisateur est connecté (sauf pour login/register)
if (!isset($_SESSION['user_id']) && 
    !in_array($controller, ['auth', 'home']) && 
    !in_array($action, ['login', 'register', 'index'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Déterminer le contrôleur à charger
$controller_name = ucfirst($controller) . 'Controller';
$controller_file = 'controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    $controller_instance = new $controller_name();
    
    // Vérifier si la méthode existe
    if (method_exists($controller_instance, $action)) {
        $controller_instance->$action();
    } else {
        // Action non trouvée
        header('Location: index.php?controller=error&action=not_found');
    }
} else {
    // Contrôleur non trouvé
    header('Location: index.php?controller=error&action=not_found');
}
?>
