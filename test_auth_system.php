<?php
// Test script to validate authentication functionality
echo "=== Zoomwheels Authentication System Test ===\n\n";

// Test 1: Database Connection and User Count
echo "1. Testing Database Connection...\n";
require_once 'vendor/autoload.php';
require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "✓ Database connection successful\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Total users in database: {$result['count']}\n";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query('SELECT username, email FROM users LIMIT 3');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✓ Sample users:\n";
        foreach ($users as $user) {
            echo "  - {$user['username']} ({$user['email']})\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Session Class
echo "2. Testing Session Class...\n";
try {
    $session = new \App\Core\Session();
    echo "✓ Session class loaded successfully\n";
    
    // Test session methods exist
    if (method_exists($session, 'isLoggedIn')) {
        echo "✓ isLoggedIn method exists\n";
    }
    if (method_exists($session, 'setUser')) {
        echo "✓ setUser method exists\n";
    }
    if (method_exists($session, 'destroy')) {
        echo "✓ destroy method exists\n";
    }
} catch (Exception $e) {
    echo "✗ Session class error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: UserRepository
echo "3. Testing UserRepository...\n";
try {
    $userRepo = new \App\Repositories\UserRepository($pdo);
    echo "✓ UserRepository loaded successfully\n";
    
    // Test methods exist
    if (method_exists($userRepo, 'findByUsername')) {
        echo "✓ findByUsername method exists\n";
    }
    if (method_exists($userRepo, 'findByEmail')) {
        echo "✓ findByEmail method exists\n";
    }
    if (method_exists($userRepo, 'create')) {
        echo "✓ create method exists\n";
    }
} catch (Exception $e) {
    echo "✗ UserRepository error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: AuthController
echo "4. Testing AuthController...\n";
try {
    // Mock request object
    $request = new \App\HTTP\Request();
    $userRepo = new \App\Repositories\UserRepository($pdo);
    $session = new \App\Core\Session();
    
    $authController = new \App\Controllers\AuthController($request, $userRepo, $session);
    echo "✓ AuthController loaded successfully\n";
    
    // Test methods exist
    if (method_exists($authController, 'login')) {
        echo "✓ login method exists\n";
    }
    if (method_exists($authController, 'logout')) {
        echo "✓ logout method exists\n";
    }
    if (method_exists($authController, 'showDashboard')) {
        echo "✓ showDashboard method exists\n";
    }
    if (method_exists($authController, 'showLoginForm')) {
        echo "✓ showLoginForm method exists\n";
    }
} catch (Exception $e) {
    echo "✗ AuthController error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: View Files
echo "5. Testing View Files...\n";
$viewFiles = [
    'app/Views/users/login.php',
    'app/Views/users/register.php', 
    'app/Views/users/dashboard.php'
];

foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} missing\n";
    }
}

echo "\n";

// Test 6: Routes
echo "6. Testing Routes File...\n";
if (file_exists('routings/routes.php')) {
    echo "✓ routes.php exists\n";
    $routes = file_get_contents('routings/routes.php');
    
    $requiredRoutes = [
        "route('/', 'AuthController@showLoginForm')",
        "route('/login', 'AuthController@showLoginForm')",
        "route('/login', 'AuthController@login', 'POST')",
        "route('/logout', 'AuthController@logout')",
        "route('/dashboard', 'AuthController@showDashboard')",
        "route('/register', 'AuthController@showRegisterForm')",
        "route('/register', 'AuthController@register', 'POST')"
    ];
    
    foreach ($requiredRoutes as $route) {
        if (strpos($routes, $route) !== false) {
            echo "✓ Route found: {$route}\n";
        } else {
            echo "✗ Route missing: {$route}\n";
        }
    }
} else {
    echo "✗ routes.php missing\n";
}

echo "\n=== Test Complete ===\n";
