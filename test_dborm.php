<?php
require_once 'vendor/autoload.php';

use App\Core\DBORM;
use App\Repositories\UserRepository;

echo "Testing DBORM connection...\n";
try {
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    echo "DBORM connected successfully\n";
    
    $userRepo = new UserRepository($dborm);
    echo "UserRepository created successfully\n";
    
    // Test a simple select operation
    echo "Testing select operation...\n";
    $users = $userRepo->getAll();
    echo "Users found: " . count($users) . "\n";
    
    // Test the specific query that might be causing issues
    echo "Testing getById operation...\n";
    $user = $userRepo->getById(1);
    echo "User query result: " . (empty($user) ? "empty" : "found") . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
