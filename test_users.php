<?php
require_once 'vendor/autoload.php';
require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $stmt = $pdo->query('SELECT username, email, first_name, last_name FROM users LIMIT 5');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($users) {
        echo "Existing users:\n";
        foreach ($users as $user) {
            echo "- {$user['username']} ({$user['email']}) - {$user['first_name']} {$user['last_name']}\n";
        }
    } else {
        echo "No users found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
