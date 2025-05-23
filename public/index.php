<?php
use App\Core\DBORM;
use App\Repositories\UserRepository;
use App\Repositories\RentalRepository;
use App\Core\Request;
use App\Controllers\UserController;
use App\Controllers\RentalController;
use App\Core\Router;
use App\Core\RouteMatcher;

// Initialize the DBORM connection for repositories using iDBFuncs
$dborm = new DBORM('mysql:host=localhost;dbname=rest_api','root','lingco.0576');

// Initialize the user repository with DBORM
$userRepository = new UserRepository($dborm);

// Initialize the request object
$request = new Request();

// Initialize the user controller with dependencies
$controller = new UserController($userRepository, $request);

// Initialize the student repository and controller
$studentRepository = new StudentRepository($dborm);
$studentController = new StudentController($studentRepository, $request);

// Initialize the rental repository and controller with DBORM
$rentalRepository = new RentalRepository($dborm);
$rentalController = new RentalController($rentalRepository, $request);

// Load routes
$routes = include __DIR__ . '/routes.php';

// Initialize the router
$router = new Router($request, new RouteMatcher());

// Register routes
foreach ($routes as $route) {
    $router->addRoute($route['method'], $route['path'], $route['handler']);
}

// Dispatch the request
$response = $router->dispatch();

// Send the response
http_response_code($response->getStatusCode());
header('Content-Type: application/json');
echo $response->getBody();