<?php
echo "Testing basic components...\n";

// Test autoloader
if (file_exists('vendor/autoload.php')) {
    echo "✓ Autoloader found\n";
    require_once 'vendor/autoload.php';
    echo "✓ Autoloader loaded\n";
} else {
    echo "✗ Autoloader not found\n";
    exit(1);
}

// Test database config
if (file_exists('config/database.php')) {
    echo "✓ Database config found\n";
    require_once 'config/database.php';
    echo "✓ Database config loaded\n";
} else {
    echo "✗ Database config not found\n";
    exit(1);
}

echo "Basic test complete.\n";
