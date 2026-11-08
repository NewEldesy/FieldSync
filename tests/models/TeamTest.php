<?php
// Basic Test for Team Model
// This test will interact with the database. Ensure you have a test DB or backup.

// Adjust path to reach the project root and then to config/models.
// This assumes 'tests' is at the project root.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Team.php';
require_once __DIR__ . '/../../models/User.php'; // For creating a dummy user for team creation if needed
require_once __DIR__ . '/../../models/TeamMember.php'; // For getMembers test

echo "Running Team Model Tests...\n";

// Database Connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "Failed to connect to database. Halting tests.\n";
    exit;
}

$test_team_id = null;
$test_user_id = null; // If needed for created_by or members

// Helper function for assertions
function assertTest($condition, $message) {
    if ($condition) {
        echo "PASS: " . $message . "\n";
    } else {
        echo "FAIL: " . $message . "\n";
    }
}

// --- Test Scenarios ---

// 1. Test Team Creation
function testCreateTeam($db_conn) {
    global $test_team_id;
    echo "\nTesting Team Creation...\n";
    $team = new Team($db_conn);
    $team->team_name = "Test Team Alpha " . uniqid();
    $team->description = "A temporary team for testing purposes.";
    
    $result = $team->save();
    assertTest($result === true, "Team created successfully.");
    assertTest($team->team_id !== null, "Team ID is set after creation.");
    if ($team->team_id) {
        $test_team_id = $team->team_id;
    }
}

// 2. Test Finding a Team by ID
function testFindTeamById($db_conn) {
    global $test_team_id;
    echo "\nTesting Finding Team by ID...\n";
    if (!$test_team_id) {
        echo "SKIP: No test_team_id set from creation test.\n";
        return;
    }
    
    $team = Team::findById($db_conn, $test_team_id);
    assertTest($team !== null, "Team found by ID.");
    if ($team) {
        assertTest($team->team_id == $test_team_id, "Found team has correct ID.");
        assertTest(strpos($team->team_name, "Test Team Alpha") === 0, "Found team has correct name prefix.");
    }
}

// 3. Test Updating a Team
function testUpdateTeam($db_conn) {
    global $test_team_id;
    echo "\nTesting Team Update...\n";
    if (!$test_team_id) {
        echo "SKIP: No test_team_id set from creation test.\n";
        return;
    }
    
    $team = Team::findById($db_conn, $test_team_id);
    if (!$team) {
        echo "SKIP: Cannot find team to update.\n";
        return;
    }
    
    $original_name = $team->team_name;
    $updated_description = "Updated description for Test Team Alpha.";
    $team->description = $updated_description;
    $team->team_name = $original_name . " (Updated)"; // Ensure name change doesn't cause issues if unique
    
    $result = $team->save();
    assertTest($result === true, "Team updated successfully.");
    
    $updated_team = Team::findById($db_conn, $test_team_id);
    assertTest($updated_team !== null, "Updated team found.");
    if ($updated_team) {
        assertTest($updated_team->description === $updated_description, "Team description was updated.");
        assertTest($updated_team->team_name === $original_name . " (Updated)", "Team name was updated.");
    }
}

// 4. Test Finding All Teams
function testFindAllTeams($db_conn) {
    echo "\nTesting Finding All Teams...\n";
    // Create a couple of temporary teams to ensure findAll has something to find
    $team1 = new Team($db_conn);
    $team1->team_name = "FindAll Test Team 1 " . uniqid();
    $team1->save();
    $temp_team1_id = $team1->team_id;

    $team2 = new Team($db_conn);
    $team2->team_name = "FindAll Test Team 2 " . uniqid();
    $team2->save();
    $temp_team2_id = $team2->team_id;

    $teams = Team::findAll($db_conn);
    assertTest(is_array($teams), "findAll returns an array.");
    assertTest(count($teams) >= 2, "findAll returns at least two teams (the ones just created + pre-existing).");

    $found_temp1 = false;
    $found_temp2 = false;
    foreach($teams as $t) {
        if ($t->team_id == $temp_team1_id) $found_temp1 = true;
        if ($t->team_id == $temp_team2_id) $found_temp2 = true;
    }
    assertTest($found_temp1, "findAll result includes temp_team1_id.");
    assertTest($found_temp2, "findAll result includes temp_team2_id.");

    // Cleanup temporary teams for findAll
    if($temp_team1_id) { $team1_del = new Team($db_conn); $team1_del->team_id = $temp_team1_id; $team1_del->delete(); }
    if($temp_team2_id) { $team2_del = new Team($db_conn); $team2_del->team_id = $temp_team2_id; $team2_del->delete(); }
}

// 5. Test Getting Team Members (Basic check, assumes TeamMember model works)
function testGetTeamMembers($db_conn) {
    global $test_team_id;
    echo "\nTesting Get Team Members...\n";
    if (!$test_team_id) {
        echo "SKIP: No test_team_id set for getMembers test.\n";
        return;
    }

    $team = Team::findById($db_conn, $test_team_id);
    if (!$team) {
        echo "SKIP: Cannot find team for getMembers test.\n";
        return;
    }
    
    // At this point, no members are added yet via these tests.
    // This test will call getMembers which should return an empty array.
    // A more thorough test would involve creating users and team members.
    $members = $team->getMembers(); // Uses TeamMember::findByTeamId
    assertTest(is_array($members), "getMembers returns an array.");
    assertTest(empty($members), "getMembers returns an empty array for a new team without members.");
}


// 6. Test Team Deletion
function testDeleteTeam($db_conn) {
    global $test_team_id;
    echo "\nTesting Team Deletion...\n";
    if (!$test_team_id) {
        echo "SKIP: No test_team_id set from creation test to delete.\n";
        return;
    }
    
    $team = new Team($db_conn);
    $team->team_id = $test_team_id;
    
    $result = $team->delete();
    assertTest($result === true, "Team deleted successfully.");
    
    $deleted_team = Team::findById($db_conn, $test_team_id);
    assertTest($deleted_team === null, "Deleted team is not found.");
    $test_team_id = null; // Clear it after deletion
}


// --- Run Tests ---
testCreateTeam($db);
testFindTeamById($db);
testUpdateTeam($db);
// testGetTeamMembers($db); // Test before general findAll to avoid finding its members yet
testFindAllTeams($db); // Creates and cleans up its own teams
testDeleteTeam($db); // Deletes the team created by testCreateTeam

echo "\nTeam Model Tests Finished.\n";

?>
