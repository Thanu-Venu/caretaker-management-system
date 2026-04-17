<?php
require_once 'app/init.php';

// Test if caretaker can be loaded
try {
    $controller = new CaretakerController();
    echo "CaretakerController loaded successfully<br>";
    
    // Test dashboard method
    $controller->ct_dashboard();
    echo "Dashboard method executed successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>Trace: " . $e->getTraceAsString();
}
?>
