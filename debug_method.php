<?php
require_once 'vendor/autoload.php';

use App\Core\DBORM;

echo "Testing getMethod parameter...\n";
try {
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    
    // Create a test query
    $query = $dborm->table('users')->select()->from('users');
    
    // Use reflection to debug the _runGetQuery method
    $reflection = new ReflectionClass($dborm);
    $method = $reflection->getMethod('_runGetQuery');
    $method->setAccessible(true);
    
    echo "Testing with 'DBORM::getAll'...\n";
    
    // Instead of calling getAll(), let's see what __METHOD__ returns
    echo "Current class: " . get_class($dborm) . "\n";
    echo "Expected method string should be: App\\Core\\DBORM::getAll\n";
    
    // Let's test the actual getAll call
    $result = $query->getAll();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
