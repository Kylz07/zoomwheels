<?php
require_once 'vendor/autoload.php';

use App\Core\DBORM;

echo "Testing basic DBORM functionality...\n";
try {
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    echo "DBORM connected successfully\n";
    
    // Test table method
    $query = $dborm->table('users');
    echo "table() method succeeded\n";
    
    // Test select method
    $query = $query->select();
    echo "select() method succeeded\n";
    
    // Test from method
    $query = $query->from('users');
    echo "from() method succeeded\n";
    
    // Show the query being built
    echo "Query: " . $query->showQuery() . "\n";
    
    // Test getAll method
    echo "Attempting getAll()...\n";
    $result = $query->getAll();
    echo "getAll() succeeded, returned: " . gettype($result) . "\n";
    echo "Count: " . count($result) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
