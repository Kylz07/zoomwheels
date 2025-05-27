<?php
require_once 'vendor/autoload.php';

use App\Core\DBORM;

echo "Testing database connection...\n";

try {
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    echo "✓ DBORM connection successful\n";
    
    // Test basic query
    $result = $dborm->query("SELECT COUNT(*) as count FROM users");
    echo "✓ Query executed successfully\n";
    echo "✓ User count: " . $result[0]['count'] . "\n";
    
    // Test if we have any users
    if ($result[0]['count'] > 0) {
        $users = $dborm->query("SELECT username, email FROM users LIMIT 3");
        echo "✓ Sample users:\n";
        foreach ($users as $user) {
            echo "  - {$user['username']} ({$user['email']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\nTesting complete.\n";
