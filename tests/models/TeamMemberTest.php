<?php
// Basic Test for TeamMember Model

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Team.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/TeamMember.php';

echo "Running TeamMember Model Tests...\n";

// Database Connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "Failed to connect to database. Halting tests.\n";
    exit;
}

$test_team_id_for_member_tests = null;
$test_user_id_for_member_tests = null;
$test_team_member_id = null;

// Helper function for assertions (can be moved to a shared test_helper.php)
if (!function_exists('assertTest')) { // Avoid re-declaration if included in multiple files
    function assertTest($condition, $message) {
        if ($condition) {
            echo "PASS: " . $message . "\n";
        } else {
            echo "FAIL: " . $message . "\n";
        }
    }
}

// --- Setup: Create a temporary Team and User for tests ---
function setupTeamAndUserForMemberTests($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests;

    // Create User
    $user = new User($db_conn);
    $user->name = "Test User for Member " . uniqid();
    $user->email = "testuser_member_" . uniqid() . "@example.com";
    $user->password = "password123"; // Will be hashed by User model's create
    $user->role = "user";
    if ($user->create()) {
        $test_user_id_for_member_tests = $user->id;
        echo "SETUP: Created test user with ID: " . $test_user_id_for_member_tests . "\n";
    } else {
        echo "SETUP_FAIL: Could not create test user. Halting TeamMember tests.\n";
        exit;
    }

    // Create Team
    $team = new Team($db_conn);
    $team->team_name = "Test Team for Member " . uniqid();
    if ($team->save()) {
        $test_team_id_for_member_tests = $team->team_id;
        echo "SETUP: Created test team with ID: " . $test_team_id_for_member_tests . "\n";
    } else {
        echo "SETUP_FAIL: Could not create test team. Halting TeamMember tests.\n";
        // Attempt to delete the created user if team creation fails
        if ($test_user_id_for_member_tests) {
            $user_to_delete = new User($db_conn);
            $user_to_delete->id = $test_user_id_for_member_tests;
            // User model might not have a direct delete, or it might cascade.
            // For simplicity, we'll assume direct deletion is possible or not critical if setup fails this early.
        }
        exit;
    }
}

// --- Teardown: Remove the temporary Team and User ---
function teardownTeamAndUserForMemberTests($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests;

    if ($test_team_id_for_member_tests) {
        $team = new Team($db_conn);
        $team->team_id = $test_team_id_for_member_tests;
        if ($team->delete()) {
            echo "TEARDOWN: Deleted test team with ID: " . $test_team_id_for_member_tests . "\n";
        } else {
            echo "TEARDOWN_WARN: Could not delete test team with ID: " . $test_team_id_for_member_tests . "\n";
        }
    }

    if ($test_user_id_for_member_tests) {
        // User model needs a delete method. Assuming it exists or a manual query.
        // For now, we'll simulate:
        $user_delete_query = "DELETE FROM users WHERE id = :id";
        $stmt = $db_conn->prepare($user_delete_query);
        $stmt->bindParam(':id', $test_user_id_for_member_tests);
        if ($stmt->execute()) {
            echo "TEARDOWN: Deleted test user with ID: " . $test_user_id_for_member_tests . "\n";
        } else {
            echo "TEARDOWN_WARN: Could not delete test user with ID: " . $test_user_id_for_member_tests . "\n";
        }
    }
}


// --- Test Scenarios ---

// 1. Test Adding a Team Member
function testAddTeamMember($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests, $test_team_member_id;
    echo "\nTesting Add Team Member...\n";
    
    $member = new TeamMember($db_conn);
    $member->team_id = $test_team_id_for_member_tests;
    $member->user_id = $test_user_id_for_member_tests;
    $member->role = "Test Role";
    
    $result = $member->save();
    assertTest($result === true, "Team member added successfully.");
    assertTest($member->team_member_id !== null, "Team member ID is set after creation.");
    if($member->team_member_id) {
        $test_team_member_id = $member->team_member_id;
    }
}

// 2. Test Finding Members by Team ID
function testFindByTeamId($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests;
    echo "\nTesting Find Members by Team ID...\n";
    
    $members = TeamMember::findByTeamId($db_conn, $test_team_id_for_member_tests);
    assertTest(is_array($members), "findByTeamId returns an array.");
    assertTest(count($members) > 0, "findByTeamId returns at least one member for the test team.");
    if (count($members) > 0) {
        $found = false;
        foreach($members as $m) {
            if ($m->user_id == $test_user_id_for_member_tests && $m->team_id == $test_team_id_for_member_tests) {
                $found = true;
                break;
            }
        }
        assertTest($found, "The added test member was found by team ID.");
    }
}

// 3. Test Finding Memberships by User ID
function testFindByUserId($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests;
    echo "\nTesting Find Memberships by User ID...\n";

    $memberships = TeamMember::findByUserId($db_conn, $test_user_id_for_member_tests);
    assertTest(is_array($memberships), "findByUserId returns an array.");
    assertTest(count($memberships) > 0, "findByUserId returns at least one membership for the test user.");
     if (count($memberships) > 0) {
        $found = false;
        foreach($memberships as $m) {
            if ($m->user_id == $test_user_id_for_member_tests && $m->team_id == $test_team_id_for_member_tests) {
                $found = true;
                break;
            }
        }
        assertTest($found, "The added test membership was found by user ID.");
    }
}

// 4. Test Is User Member
function testIsUserMember($db_conn) {
    global $test_team_id_for_member_tests, $test_user_id_for_member_tests;
    echo "\nTesting Is User Member...\n";

    $is_member = TeamMember::isUserMember($db_conn, $test_team_id_for_member_tests, $test_user_id_for_member_tests);
    assertTest($is_member === true, "isUserMember correctly identifies an existing member.");

    // Test with a non-existent user ID (assuming -99 is not a real user ID)
    $is_not_member = TeamMember::isUserMember($db_conn, $test_team_id_for_member_tests, -99);
    assertTest($is_not_member === false, "isUserMember correctly identifies a non-member.");
}

// 5. Test Removing a Team Member
function testRemoveTeamMember($db_conn) {
    global $test_team_member_id, $test_team_id_for_member_tests, $test_user_id_for_member_tests;
    echo "\nTesting Remove Team Member...\n";
    if (!$test_team_member_id) {
        echo "SKIP: No test_team_member_id set from add member test to delete.\n";
        return;
    }

    $member = new TeamMember($db_conn);
    $member->team_member_id = $test_team_member_id;
    
    $result = $member->delete();
    assertTest($result === true, "Team member removed successfully.");

    // Verify the member is actually removed
    $is_still_member = TeamMember::isUserMember($db_conn, $test_team_id_for_member_tests, $test_user_id_for_member_tests);
    assertTest($is_still_member === false, "isUserMember confirms member removal.");
}


// --- Run Tests ---
setupTeamAndUserForMemberTests($db);

testAddTeamMember($db);
testFindByTeamId($db);
testFindByUserId($db);
testIsUserMember($db);
testRemoveTeamMember($db);

teardownTeamAndUserForMemberTests($db);

echo "\nTeamMember Model Tests Finished.\n";

?>
