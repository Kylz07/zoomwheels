<?php
require_once 'vendor/autoload.php';

use App\Core\DBORM;
use App\Repositories\UserRepository;

echo "Testing with fresh DBORM instances...\n";
try {
    // Test UserRepository with fresh instances
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    $userRepo = new UserRepository($dborm);
    
    echo "Testing getAll()...\n";
    $users = $userRepo->getAll();
    echo "Success! Found " . count($users) . " users\n";
    
    echo "Testing getById()...\n";
    $user = $userRepo->getById(1);
    echo "Success! User query returned: " . (empty($user) ? "empty" : "data") . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
