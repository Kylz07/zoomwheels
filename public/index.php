<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\DBORM;
use App\Core\Database; // Added: Import the Database class
use App\Repositories\UserRepository;
use App\Repositories\RentalRepository;
use App\Core\Request;
use App\Controllers\UserController;
use App\Controllers\RentalController;
use App\Controllers\AuthController;
use App\Services\JwtService;
use App\Core\Router;
use App\Core\RouteMatcher;



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

// Initialize the user repository with DBORM
$userRepository = new UserRepository($dborm);

// Initialize the request object
$request = new Request();

// Initialize JWT service (shared across controllers)
$jwtService = new JwtService($userRepository);

// Initialize the user controller with dependencies
$controller = new UserController($userRepository, $request, $jwtService);

// Initialize the rental repository and controller
$rentalRepository = new RentalRepository($dborm, $database); // Updated: Pass both DBORM and Database instances
$rentalController = new RentalController($rentalRepository, $request, $jwtService);

// Initialize the auth controller
$authController = new AuthController($userRepository, $request);

// Load routes
$routes = include __DIR__ . '/../routings/routes.php';

// Initialize the router
$router = new Router($request, new RouteMatcher());

// Register routes
foreach ($routes as $route) {
    $router->addRoute($route['method'], $route['path'], $route['handler']);
}

// Dispatch the request
$response = $router->dispatch();

// Send the response using the new send() method
$response->send();