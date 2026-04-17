<?php
// Debug script to test caretaker page loading
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Caretaker Page Debug</h2>";

// Test 1: Check if required files exist
$requiredFiles = [
    'app/controllers/CaretakerController.php',
    'app/models/CaretakerModel.php',
    'app/models/LeaveModel.php',
    'app/models/ComplaintModel.php',
    'app/models/ProfileChangeRequestModel.php',
    'app/views/caretaker/ct_dashboard.php',
    'app/views/templates/caretaker/caretaker_layout_head.php',
    'app/views/templates/caretaker/ct_header.php',
    'app/views/templates/caretaker/ct_sidebar.php'
];

echo "<h3>Required Files Check:</h3>";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "  <span style='color:green'>EXISTS</span>: $file<br>";
    } else {
        echo "  <span style='color:red'>MISSING</span>: $file<br>";
    }
}

// Test 2: Check if URLROOT is defined
echo "<h3>Configuration Check:</h3>";
if (defined('URLROOT')) {
    echo "  <span style='color:green'>URLROOT defined</span>: " . URLROOT . "<br>";
} else {
    echo "  <span style='color:red'>URLROOT not defined</span><br>";
}

// Test 3: Check session state
echo "<h3>Session Check:</h3>";
if (session_status() === PHP_SESSION_NONE) {
    echo "  <span style='color:orange'>No session started</span><br>";
} else {
    echo "  <span style='color:green'>Session active</span><br>";
    if (!empty($_SESSION['user'])) {
        echo "  <span style='color:green'>User in session</span>: " . htmlspecialchars($_SESSION['user']['name'] ?? 'Unknown') . "<br>";
        echo "  <span style='color:green'>Role</span>: " . htmlspecialchars($_SESSION['role'] ?? 'Not set') . "<br>";
    } else {
        echo "  <span style='color:red'>No user in session</span><br>";
    }
}

// Test 4: Try to load CaretakerController
echo "<h3>Controller Loading Test:</h3>";
try {
    // Simulate the app initialization
    require_once 'app/init.php';
    
    // Check if AuthSession is available
    if (class_exists('AuthSession')) {
        echo "  <span style='color:green'>AuthSession class available</span><br>";
    } else {
        echo "  <span style='color:red'>AuthSession class missing</span><br>";
    }
    
    // Try to create CaretakerController (this will fail if not authenticated)
    echo "  Attempting to load CaretakerController...<br>";
    $caretakerController = new CaretakerController();
    echo "  <span style='color:green'>CaretakerController loaded successfully</span><br>";
    
} catch (Exception $e) {
    echo "  <span style='color:red'>Error loading CaretakerController</span>: " . htmlspecialchars($e->getMessage()) . "<br>";
} catch (Error $e) {
    echo "  <span style='color:red'>Fatal error loading CaretakerController</span>: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 5: Check database connection
echo "<h3>Database Test:</h3>";
try {
    $db = new Database();
    if ($db->conn) {
        echo "  <span style='color:green'>Database connection successful</span><br>";
        
        // Check if caretakers table exists
        $result = $db->conn->query("SHOW TABLES LIKE 'caretakers'");
        if ($result && $result->num_rows > 0) {
            echo "  <span style='color:green'>Caretakers table exists</span><br>";
        } else {
            echo "  <span style='color:red'>Caretakers table missing</span><br>";
        }
    } else {
        echo "  <span style='color:red'>Database connection failed</span><br>";
    }
} catch (Exception $e) {
    echo "  <span style='color:red'>Database error</span>: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<br><h3>Test URLs:</h3>";
echo "<a href='" . (defined('URLROOT') ? URLROOT : '/CMA') . "/public/?url=auth/login' target='_blank'>Login Page</a><br>";
echo "<a href='" . (defined('URLROOT') ? URLROOT : '/CMA') . "/public/?url=caretaker/ct_dashboard' target='_blank'>Caretaker Dashboard</a><br>";

echo "<br><h3>Debug Complete</h3>";
echo "If you see errors above, they indicate what needs to be fixed for the caretaker page to work.";

?>
