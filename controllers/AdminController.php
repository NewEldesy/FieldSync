<?php
class AdminController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->requireAdmin();
    }

    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            setFlashMessage('danger', 'Accès réservé aux administrateurs.');
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
    }

    // Tableau de bord admin
    public function index() {
        // Statistiques globales
        $stats = [];

        $stmt = $this->db->query("SELECT COUNT(*) as total, SUM(role='admin') as admins, SUM(role='manager') as managers, SUM(role='user') as users FROM users");
        $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->query("SELECT COUNT(*) as total, SUM(status='pending') as pending, SUM(status='sent') as sent, SUM(status='failed') as failed FROM sms_notifications");
        $stats['sms'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->query("SELECT COUNT(*) as total, SUM(status='confirmed') as confirmed, SUM(status='pending') as pending, SUM(status='cancelled') as cancelled FROM visits");
        $stats['visits'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // 10 dernières entrées du journal
        $log = new ActivityLog($this->db);
        $activity_stmt = $log->readRecent(10);

        include 'views/admin/index.php';
    }

    // Gestion des utilisateurs
    public function users() {
        $user = new User($this->db);
        $users_stmt = $user->readAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité.');
                header('Location: index.php?controller=admin&action=users');
                exit;
            }

            $target_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $new_role  = in_array($_POST['role'] ?? '', ['admin', 'manager', 'user']) ? $_POST['role'] : 'user';

            // Empêcher l'admin de rétrograder son propre compte
            if ($target_id === (int) $_SESSION['user_id'] && $new_role !== 'admin') {
                setFlashMessage('danger', 'Vous ne pouvez pas modifier votre propre rôle.');
            } else {
                $stmt = $this->db->prepare("UPDATE users SET role = :role WHERE id = :id");
                $stmt->bindParam(':role', $new_role);
                $stmt->bindParam(':id',   $target_id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    setFlashMessage('success', 'Rôle mis à jour.');
                } else {
                    setFlashMessage('danger', 'Erreur lors de la mise à jour.');
                }
            }
            header('Location: index.php?controller=admin&action=users');
            exit;
        }

        include 'views/admin/users.php';
    }

    // Paramètres système
    public function settings() {
        $settings = new SystemSettings($this->db);
        $settings_stmt = $settings->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                setFlashMessage('danger', 'Erreur de sécurité.');
                header('Location: index.php?controller=admin&action=settings');
                exit;
            }

            $allowed_keys = ['app_name', 'sms_provider', 'daily_summary_time', 'max_file_size'];
            $errors = [];

            foreach ($allowed_keys as $key) {
                if (isset($_POST[$key])) {
                    $value = trim($_POST[$key]);
                    try {
                        $settings->set($key, $value);
                    } catch (InvalidArgumentException $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }

            if (empty($errors)) {
                setFlashMessage('success', 'Paramètres mis à jour avec succès.');
            } else {
                foreach ($errors as $e) {
                    setFlashMessage('danger', $e);
                }
            }
            header('Location: index.php?controller=admin&action=settings');
            exit;
        }

        // Recharger après eventuels changements
        $settings_stmt = $settings->getAll();
        include 'views/admin/settings.php';
    }
}
?>
