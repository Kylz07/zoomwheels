<?php
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
];