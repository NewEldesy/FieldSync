<?php
class TeamController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Récupérer toutes les équipes avec le nombre de membres
        $query = "SELECT t.*, COUNT(tm.team_member_id) as member_count
                  FROM teams t
                  LEFT JOIN team_members tm ON t.team_id = tm.team_id
                  GROUP BY t.team_id
                  ORDER BY t.team_name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = "Équipes";
        $_topbar_title = "Gestion des équipes";
        include 'views/teams/index.php';
    }

    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            setFlashMessage('danger', 'ID d\'équipe invalide.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $team = Team::findById($this->db, $id);
        if (!$team) {
            setFlashMessage('danger', 'Équipe non trouvée.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        // Membres avec nom d'utilisateur (JOIN)
        $query = "SELECT tm.*, u.name as user_name
                  FROM team_members tm
                  JOIN users u ON tm.user_id = u.id
                  WHERE tm.team_id = :team_id
                  ORDER BY tm.joined_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $id);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Utilisateurs pas encore dans l'équipe
        $query2 = "SELECT id, name FROM users
                   WHERE id NOT IN (
                       SELECT user_id FROM team_members WHERE team_id = :team_id
                   )
                   ORDER BY name ASC";
        $stmt2 = $this->db->prepare($query2);
        $stmt2->bindParam(':team_id', $id);
        $stmt2->execute();
        $users_not_in_team = $stmt2->fetchAll(PDO::FETCH_OBJ);

        $pageTitle = "Équipe : " . htmlspecialchars($team->team_name);
        $_topbar_title = htmlspecialchars($team->team_name);
        include 'views/teams/view.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Seuls les admins peuvent créer des équipes
        if ($_SESSION['user_role'] !== 'admin') {
            setFlashMessage('danger', 'Accès refusé.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $team_name = trim($_POST['team_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($team_name)) {
                setFlashMessage('danger', 'Le nom de l\'équipe est obligatoire.');
                $pageTitle = "Créer une équipe";
                $_topbar_title = "Nouvelle équipe";
                include 'views/teams/create.php';
                return;
            }

            $team = new Team($this->db);
            $team->team_name = $team_name;
            $team->description = $description ?: null;

            if ($team->save()) {
                setFlashMessage('success', 'Équipe créée avec succès.');
                header('Location: index.php?controller=team&action=view&id=' . $team->team_id);
            } else {
                setFlashMessage('danger', 'Erreur lors de la création de l\'équipe.');
                header('Location: index.php?controller=team&action=index');
            }
            exit;
        }

        $pageTitle = "Créer une équipe";
        $_topbar_title = "Nouvelle équipe";
        include 'views/teams/create.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            setFlashMessage('danger', 'Accès refusé.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            setFlashMessage('danger', 'ID d\'équipe invalide.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $team = Team::findById($this->db, $id);
        if (!$team) {
            setFlashMessage('danger', 'Équipe non trouvée.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $team_name = trim($_POST['team_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($team_name)) {
                setFlashMessage('danger', 'Le nom de l\'équipe est obligatoire.');
                $pageTitle = "Modifier l'équipe";
                $_topbar_title = "Modifier l'équipe";
                include 'views/teams/edit.php';
                return;
            }

            $team->team_name = $team_name;
            $team->description = $description ?: null;

            if ($team->save()) {
                setFlashMessage('success', 'Équipe mise à jour.');
                header('Location: index.php?controller=team&action=view&id=' . $team->team_id);
            } else {
                setFlashMessage('danger', 'Erreur lors de la mise à jour.');
                header('Location: index.php?controller=team&action=index');
            }
            exit;
        }

        $pageTitle = "Modifier l'équipe";
        $_topbar_title = "Modifier l'équipe";
        include 'views/teams/edit.php';
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            setFlashMessage('danger', 'Accès refusé.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            setFlashMessage('danger', 'ID invalide.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $team = Team::findById($this->db, $id);
        if (!$team) {
            setFlashMessage('danger', 'Équipe non trouvée.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        // Supprimer les membres puis l'équipe
        $this->db->prepare("DELETE FROM team_members WHERE team_id = :id")
                 ->execute([':id' => $id]);

        if ($team->delete()) {
            setFlashMessage('success', 'Équipe supprimée.');
        } else {
            setFlashMessage('danger', 'Erreur lors de la suppression.');
        }

        header('Location: index.php?controller=team&action=index');
        exit;
    }

    public function addMember() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            setFlashMessage('danger', 'Accès refusé.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $team_id = intval($_POST['team_id'] ?? $_GET['team_id'] ?? 0);
        $user_id = intval($_POST['user_id'] ?? 0);
        $role    = trim($_POST['role'] ?? 'member');

        if ($team_id <= 0 || $user_id <= 0) {
            setFlashMessage('danger', 'Paramètres invalides.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        // Vérifier que l'utilisateur n'est pas déjà membre
        if (TeamMember::isUserMember($this->db, $team_id, $user_id)) {
            setFlashMessage('warning', 'Cet utilisateur est déjà membre de cette équipe.');
            header('Location: index.php?controller=team&action=view&id=' . $team_id);
            exit;
        }

        $member = new TeamMember($this->db);
        $member->team_id = $team_id;
        $member->user_id = $user_id;
        $member->role    = $role ?: 'member';

        if ($member->save()) {
            setFlashMessage('success', 'Membre ajouté à l\'équipe.');
        } else {
            setFlashMessage('danger', 'Erreur lors de l\'ajout du membre.');
        }

        header('Location: index.php?controller=team&action=view&id=' . $team_id);
        exit;
    }

    public function removeMember() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            setFlashMessage('danger', 'Accès refusé.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        $team_member_id = isset($_GET['team_member_id']) ? intval($_GET['team_member_id']) : 0;
        if ($team_member_id <= 0) {
            setFlashMessage('danger', 'ID de membre invalide.');
            header('Location: index.php?controller=team&action=index');
            exit;
        }

        // Récupérer l'enregistrement pour connaître le team_id (redirection)
        $stmt = $this->db->prepare("SELECT team_id FROM team_members WHERE team_member_id = :id LIMIT 1");
        $stmt->bindParam(':id', $team_member_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $team_id = $row ? intval($row['team_id']) : 0;

        $member = new TeamMember($this->db);
        $member->team_member_id = $team_member_id;

        if ($member->delete()) {
            setFlashMessage('success', 'Membre retiré de l\'équipe.');
        } else {
            setFlashMessage('danger', 'Erreur lors de la suppression du membre.');
        }

        if ($team_id > 0) {
            header('Location: index.php?controller=team&action=view&id=' . $team_id);
        } else {
            header('Location: index.php?controller=team&action=index');
        }
        exit;
    }
}
