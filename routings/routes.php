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
    
    // Dashboard route - now handled by RentalController
    ['method' => 'GET', 'path' => '/rentals', 'handler' => function () use ($rentalController) {
        return $rentalController->showRentalsPage();
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
    // --- Delete routes ---
    ['method' => 'GET', 'path' => '/rentals/delete/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->showDeleteForm($id);
    }],
    ['method' => 'POST', 'path' => '/rentals/delete/{id}', 'handler' => function ($id) use ($rentalController) {
        return $rentalController->processDelete($id);
    }],
    // --- End Delete routes ---
];