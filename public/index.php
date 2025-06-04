<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\DBORM;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\RouteMatcher;
use App\Repositories\UserRepository;
use App\Repositories\RentalRepository;
use App\Controllers\UserController;
use App\Controllers\RentalController;
use App\Controllers\AuthController;
use App\Services\JwtService;
use App\Services\AuthService; 
use App\Services\CookieAuthService; 
use App\Exceptions\WebAuthenticationRequiredException;




// Database Configuration
$dbHost = 'localhost';
$dbName = 'zoomwheels';
$dbUser = 'root';
$dbPass = 'lingco.0576'; // Your actual password

// Initialize the DBORM connection for repositories using iDBFuncs
$dbormDsn = "mysql:host={$dbHost};dbname={$dbName}";
$dborm = new DBORM($dbormDsn, $dbUser, $dbPass);

// Initialize the Database connection for direct queries
$database = new Database($dbHost, $dbUser, $dbPass, $dbName);

// Initialize the request object
$request = new Request();

// Initialize the user controller with dependencies
$userRepository = new UserRepository($dborm, $database);

// Initialize JWT service (shared across controllers)
$jwtService = new JwtService($userRepository);

// Initialize CookieAuthService (shared across relevant controllers)
$cookieAuthService = new CookieAuthService($jwtService, null); // Assuming SessionService is intentionally null

$userController = new UserController($userRepository, $request, $jwtService);

// Initialize the rental repository and controller
$rentalRepository = new RentalRepository($dborm, $database); // Updated: Pass both DBORM and Database instances
$rentalController = new RentalController($rentalRepository, $request, $jwtService, $cookieAuthService); // Added cookieAuthService

// Initialize services for AuthController
$authService = new AuthService($userRepository);

// Initialize the auth controller
$authController = new AuthController($userRepository, $request, $authService, $jwtService, $cookieAuthService);

// Load routes
$routes = include __DIR__ . '/../routings/routes.php';

// Initialize the router
$router = new Router($request, new RouteMatcher());

// Register routes
foreach ($routes as $route) {
    $router->addRoute($route['method'], $route['path'], $route['handler']);
}

try {
    $response = $router->dispatch();
} catch (WebAuthenticationRequiredException $e) {
    // For web authentication failures, redirect to login with error in URL
    $encodedMessage = urlencode($e->getMessage());
    header('Location: /login?error=' . $encodedMessage);
    exit;
} catch (\Exception $e) {
    // Log unexpected errors
    error_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    // Return appropriate error response
    if (!headers_sent()) {
        $errorResponse = new Response(
            500, 
            'An unexpected error occurred. Please try again later.',
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
        $errorResponse->send();
    } else {
        echo 'An unexpected error occurred. Please try again later.';
    }
    exit;
}

// Send successful response
if (isset($response) && $response instanceof Response) {
    $response->send();
}