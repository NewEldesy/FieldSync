<?php
// Ensure basic configuration and helper functions are loaded
// These paths might need adjustment based on the actual include path setup in index.php or a bootstrap file.
// Assuming 'controllers' is one level down from the project root.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Team.php';
require_once __DIR__ . '/../models/TeamMember.php';
require_once __DIR__ . '/../models/User.php'; // For user details when adding/viewing members
require_once __DIR__ . '/../helpers/functions.php'; // For sanitizeInput, setFlashMessage etc.

class TeamController {
    private $db;

    public function __construct() {
        $database = new Database(); // From database.php
        $this->db = $database->getConnection();
    }

    public function index() {
        if (!isUserLoggedIn()) { // Assuming isUserLoggedIn() from helpers/functions.php
            redirect('index.php?controller=auth&action=login');
        }

        // TODO: Instantiate Team model
        // TODO: Call Team::findAll($this->db)
        // TODO: Load all teams and pass to the view
        // Log_message('debug', 'TeamController: index method called.');
        // require_once __DIR__ . '/../views/teams/index.php';
        echo "TeamController@index: Display list of teams (stub)";
    }

    public function view($team_id) {
         if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }
        $id = intval($team_id); // Or from $_GET['id'] depending on routing

        // TODO: Instantiate Team model
        // TODO: Call Team::findById($this->db, $id)
        // TODO: If team found, call $team->getMembers()
        // TODO: Load team data and members data and pass to the view
        // Log_message('debug', 'TeamController: view method called for team_id: ' . $id);
        // require_once __DIR__ . '/../views/teams/view.php';
        echo "TeamController@view: Display team with ID: $id (stub)";
    }

    public function create() {
        if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: CSRF token verification
            // TODO: Sanitize input: $_POST['team_name'], $_POST['description']
            // TODO: Validate data
            // TODO: Instantiate Team model
            // TODO: Set properties $team->team_name, $team->description
            // TODO: Call $team->save()
            // TODO: Set flash message (success/error)
            // TODO: Redirect to index or view
            // Log_message('debug', 'TeamController: create method POST called.');
            echo "TeamController@create: POST - Handle team creation (stub)";
        } else {
            // TODO: Display team creation form
            // Log_message('debug', 'TeamController: create method GET called.');
            // require_once __DIR__ . '/../views/teams/create.php';
            echo "TeamController@create: GET - Display team creation form (stub)";
        }
    }

    public function edit($team_id) {
        if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }
        $id = intval($team_id); // Or from $_GET['id']

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: CSRF token verification
            // TODO: Sanitize input: $_POST['team_name'], $_POST['description']
            // TODO: Validate data
            // TODO: Instantiate Team model
            // TODO: $team = Team::findById($this->db, $id)
            // TODO: If team found, set properties $team->team_name, $team->description
            // TODO: Call $team->save()
            // TODO: Set flash message (success/error)
            // TODO: Redirect to index or view
            // Log_message('debug', 'TeamController: edit method POST called for team_id: ' . $id);
            echo "TeamController@edit: POST - Handle team update for ID: $id (stub)";
        } else {
            // TODO: Instantiate Team model
            // TODO: $team = Team::findById($this->db, $id)
            // TODO: If team found, pass team data to the view
            // TODO: Display team editing form
            // Log_message('debug', 'TeamController: edit method GET called for team_id: ' . $id);
            // require_once __DIR__ . '/../views/teams/edit.php';
            echo "TeamController@edit: GET - Display team edit form for ID: $id (stub)";
        }
    }

    public function delete($team_id) {
        if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }
        // Typically, deletion might be a POST request for safety with CSRF
        // For simplicity as a stub, directly processing.
        $id = intval($team_id); // Or from $_GET['id'] or $_POST['id']

        // TODO: CSRF token verification if it's a POST request
        // TODO: Instantiate Team model
        // TODO: $team = Team::findById($this->db, $id)
        // TODO: If team found, call $team->delete()
        // TODO: Set flash message (success/error)
        // TODO: Redirect to index
        // Log_message('debug', 'TeamController: delete method called for team_id: ' . $id);
        echo "TeamController@delete: Handle team deletion for ID: $id (stub)";
    }

    public function addMember($team_id) {
        if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }
        $id = intval($team_id); // Or from $_GET['team_id']

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: CSRF token verification
            // TODO: Sanitize input: $_POST['user_id'], $_POST['role']
            // TODO: Validate data (e.g., check if team exists, user exists, user not already member)
            // TODO: Instantiate TeamMember model
            // TODO: Set properties $member->team_id, $member->user_id, $member->role
            // TODO: Call $member->save()
            // TODO: Set flash message (success/error)
            // TODO: Redirect to team view page
            // Log_message('debug', 'TeamController: addMember method POST called for team_id: ' . $id);
            echo "TeamController@addMember: POST - Handle adding member to team ID: $id (stub)";
        } else {
            // TODO: Display form to add member to team
            // TODO: May need to list users who can be added.
            // TODO: $team = Team::findById($this->db, $id);
            // TODO: $users = User::findAll($this->db); // Or a more filtered list
            // Log_message('debug', 'TeamController: addMember method GET called for team_id: ' . $id);
            // require_once __DIR__ . '/../views/teams/add_member.php';
            echo "TeamController@addMember: GET - Display form to add member to team ID: $id (stub)";
        }
    }

    public function removeMember($team_member_id) {
        if (!isUserLoggedIn()) {
            redirect('index.php?controller=auth&action=login');
        }
        // Typically, deletion might be a POST request for safety with CSRF
        $id = intval($team_member_id); // Or from $_GET['id'] or $_POST['id']

        // TODO: CSRF token verification if it's a POST request
        // TODO: Instantiate TeamMember model
        // TODO: $member = new TeamMember($this->db); $member->team_member_id = $id;
        // TODO: Call $member->delete() (or a static findById and then delete)
        // TODO: Set flash message (success/error)
        // TODO: Redirect to team view page
        // Log_message('debug', 'TeamController: removeMember method called for team_member_id: ' . $id);
        echo "TeamController@removeMember: Handle removing member with team_member_id: $id (stub)";
    }
}
?>
