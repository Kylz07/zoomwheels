<?php
// Direct test of the registration system

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\DBORM;
use App\Repositories\UserRepository;
use App\Controllers\AuthController;
use App\Core\Request;

// Simulate a registration request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/register';
$_POST = [
    'username' => 'testuser123',
    'email' => 'test@example.com',
    'first_name' => 'Test',
    'last_name' => 'User',
    'password' => 'password123',
    'confirm_password' => 'password123'
];

try {
    echo "Starting registration test...\n";
    
    // Initialize DBORM and repository
    $dborm = new DBORM('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
    $userRepository = new UserRepository($dborm);
    $request = new Request();
    $authController = new AuthController($userRepository, $request);
    
    echo "Controllers initialized...\n";
    
    // Test the registration
    $response = $authController->register();
    echo "Registration response status: " . $response->getStatusCode() . "\n";
    echo "Registration response body: " . substr($response->getBody(), 0, 200) . "...\n";
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Save output to file for debugging
file_put_contents('test_output.log', ob_get_contents());
?>
