<?php
echo "=================================\n";
echo "STARTING ALL TESTS\n";
echo "=================================\n\n";

// Ensure error reporting is on for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a consistent base directory
define('BASE_PATH', __DIR__ . '/../'); // Assuming tests directory is one level down from project root

// Simple test runner - includes and executes test files

echo "\n--- Running Model Tests ---\n";

echo "\nExecuting tests/models/TeamTest.php\n";
require_once __DIR__ . '/models/TeamTest.php';
echo "\nFinished tests/models/TeamTest.php\n";

echo "\nExecuting tests/models/TeamMemberTest.php\n";
require_once __DIR__ . '/models/TeamMemberTest.php';
echo "\nFinished tests/models/TeamMemberTest.php\n";


echo "\n--- Running Controller Tests ---\n";

echo "\nExecuting tests/controllers/TeamControllerTest.php\n";
// Controller tests might output HTML or attempt redirects if not carefully handled with output buffering
// The current stubs echo, which is fine for this basic runner.
// Session needs to be available for controller tests if they use $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    // Note: Starting session here might cause "headers already sent" if test files output content before controller methods that set headers.
    // Ideally, test files themselves manage output buffering if they call methods that output/redirect.
    // The TeamControllerTest.php already uses ob_start/ob_get_clean for some methods.
    @session_start(); // Use @ to suppress warnings if session is already started by a test file (though unlikely here)
}
require_once __DIR__ . '/controllers/TeamControllerTest.php';
echo "\nFinished tests/controllers/TeamControllerTest.php\n";


echo "\n=================================\n";
echo "ALL TESTS COMPLETED\n";
echo "=================================\n";

// Note: This simple runner doesn't provide a summary of pass/fail.
// You'd need to inspect the output of each test file.
// A proper testing framework (like PHPUnit) would handle this much better.

?>
