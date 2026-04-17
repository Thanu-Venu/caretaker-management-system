<?php
// Simple test script to verify caretaker login flow
require_once 'app/init.php';

echo "<h2>Caretaker Flow Test</h2>";

// Test 1: Check if caretaker controller exists
if (file_exists('app/controllers/CaretakerController.php')) {
    echo "✅ CaretakerController.php exists<br>";
} else {
    echo "❌ CaretakerController.php missing<br>";
}

// Test 2: Check if caretaker model exists
if (file_exists('app/models/CaretakerModel.php')) {
    echo "✅ CaretakerModel.php exists<br>";
} else {
    echo "❌ CaretakerModel.php missing<br>";
}

// Test 3: Check if dashboard view exists
if (file_exists('app/views/caretaker/ct_dashboard.php')) {
    echo "✅ Dashboard view exists<br>";
} else {
    echo "❌ Dashboard view missing<br>";
}

// Test 4: Check database connection
try {
    $db = new Database();
    if ($db->conn) {
        echo "✅ Database connection successful<br>";
        
        // Test 5: Check if caretakers table exists
        $result = $db->conn->query("SHOW TABLES LIKE 'caretakers'");
        if ($result && $result->num_rows > 0) {
            echo "✅ Caretakers table exists<br>";
            
            // Test 6: Check if there are any caretakers
            $count = $db->conn->query("SELECT COUNT(*) as count FROM caretakers")->fetch_assoc();
            if ($count['count'] > 0) {
                echo "✅ Found " . $count['count'] . " caretaker(s) in database<br>";
                
                // Show sample caretaker info (without password)
                $sample = $db->conn->query("SELECT id, name, email, status FROM caretakers LIMIT 1")->fetch_assoc();
                if ($sample) {
                    echo "📋 Sample caretaker: " . htmlspecialchars($sample['name']) . " (" . htmlspecialchars($sample['email']) . ") - Status: " . htmlspecialchars($sample['status']) . "<br>";
                }
            } else {
                echo "⚠️ No caretakers found in database<br>";
            }
        } else {
            echo "❌ Caretakers table missing<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 7: Check URL routing
echo "<br><h3>URL Routing Test</h3>";
echo "Login URL: <a href='" . URLROOT . "/public/?url=auth/login' target='_blank'>" . URLROOT . "/public/?url=auth/login</a><br>";
echo "Dashboard URL: <a href='" . URLROOT . "/public/?url=caretaker/ct_dashboard' target='_blank'>" . URLROOT . "/public/?url=caretaker/ct_dashboard</a><br>";

echo "<br><h3>Test Instructions</h3>";
echo "1. Click the Login URL above<br>";
echo "2. Enter caretaker credentials (email and password)<br>";
echo "3. Should redirect to dashboard if successful<br>";
echo "4. Test navigation through sidebar menu<br>";
echo "5. Test logout from profile menu<br>";

?>
