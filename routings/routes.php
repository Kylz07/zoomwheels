<?php
use App\Controllers\UserController;
use App\Controllers\RentalController;
use App\Controllers\AuthController;

return [
    // User routes
    ['method' => 'GET', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->getAllUsers();
    }],
    ['method' => 'GET', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->getUserById($id);
    }],
    ['method' => 'POST', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->createUser();
    }],
    ['method' => 'PUT', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->updateUser($id);
    }],
    ['method' => 'DELETE', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->deleteUser($id);
    }],
    // Rental routes
    ['method' => 'GET', 'path' => '/rentals', 'handler' => function () use ($rentalController) {
        return $rentalController->getAllRentals();
    }],
    // Place /rentals/create routes BEFORE /rentals/{id}
    ['method' => 'GET', 'path' => '/rentals/create', 'handler' => function () use ($rentalController) {
        return $rentalController->showCreateForm();
    }],
    ['method' => 'POST', 'path' => '/rentals/create', 'handler' => function () use ($rentalController) {
        return $rentalController->processCreate();
    }],
    // --- Edit routes ---
    ['method' => 'GET', 'path' => '/rentals/edit/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->showEditForm($id);
    }],
    ['method' => 'POST', 'path' => '/rentals/edit/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->processUpdate($id);
    }],
    // --- End Edit routes ---
    ['method' => 'GET', 'path' => '/rentals/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->getRentalById($id);
    }],
    ['method' => 'POST', 'path' => '/rentals', 'handler' => function () use ($rentalController) {
        return $rentalController->createRental();
    }],
    ['method' => 'PUT', 'path' => '/rentals/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->updateRental($id);
    }],
    ['method' => 'DELETE', 'path' => '/rentals/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->deleteRental($id);
    }],
    // Registration routes
    ['method' => 'GET', 'path' => '/register', 'handler' => function () use ($authController) {
        return $authController->showRegisterForm();
    }],
    ['method' => 'POST', 'path' => '/register', 'handler' => function () use ($authController) {
        return $authController->register();
    }],
    
    // Login routes
    ['method' => 'GET', 'path' => '/login', 'handler' => function () use ($authController) {
        return $authController->showLoginForm();
    }],
    ['method' => 'POST', 'path' => '/login', 'handler' => function () use ($authController) {
        return $authController->login();
    }],
    
    // Dashboard route
    ['method' => 'GET', 'path' => '/dashboard', 'handler' => function () use ($authController) {
        return $authController->showDashboard();
    }],
      // Logout route
    ['method' => 'GET', 'path' => '/logout', 'handler' => function () use ($authController) {
        return $authController->logout();
    }],
    ['method' => 'POST', 'path' => '/logout', 'handler' => function () use ($authController) {
        return $authController->logout();
    }],
    
    // Auth check route
    ['method' => 'GET', 'path' => '/auth/check', 'handler' => function () use ($authController) {
        return $authController->checkAuth();
    }],
    
    // Home/Root route - redirect to login
    ['method' => 'GET', 'path' => '/', 'handler' => function () use ($authController) {
        return $authController->showLoginForm();
    }],
    ['method' => 'GET', 'path' => '', 'handler' => function () use ($authController) {
        return $authController->showLoginForm();
    }],
    // Rental dashboard route
    ['method' => 'GET', 'path' => '/rentals-dashboard', 'handler' => function () use ($rentalController) {
        return $rentalController->showRentalsPage();
    }],
];