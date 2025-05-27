<?php
// config/init.php

// This file returns an array of application configuration settings.
// IMPORTANT: For a real application, consider using environment variables for sensitive data
// (like database passwords and JWT secrets) and never commit actual secrets to version control.
// A library like vlucas/phpdotenv can be used if you implement a .env file system.

return [
    'database' => [
        'host' => 'localhost',
        'name' => 'zoomwheels',
        'user' => 'root',
        'pass' => 'lingco.0576', // TODO: Move to environment variable
        'dsn'  => 'mysql:host=localhost;dbname=zoomwheels' // Full DSN for DBORM
    ],
    'jwt' => [
        'secret_key' => 'YOUR_VERY_STRONG_SECRET_KEY_HERE_CHANGE_ME_PLEASE', // TODO: Generate a strong key and move to environment variable
        'algorithm' => 'HS256',
        'expiry_seconds' => 3600, // 1 hour (3600 seconds)
        'issuer' => 'ZoomwheelsApp', // Optional: Your application's name or identifier
        'audience' => 'ZoomwheelsAppUsers', // Optional: Intended audience for the token
    ],
    'app' => [
        // Example: Base URL for constructing full URLs if needed, e.g., for email links
        // Ensure this correctly points to your public directory if used for URL generation.
        'base_url' => 'http://localhost/mysite/Zoomwheels/public',
        'debug_mode' => true, // Set to false in a production environment
        'timezone' => 'UTC', // Example: Set your application's default timezone
    ]
];