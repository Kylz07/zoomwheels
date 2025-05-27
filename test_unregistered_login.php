<?php
/**
 * Test what error message is shown for unregistered user login
 */

echo "=== TESTING UNREGISTERED USER LOGIN MESSAGE ===\n\n";

$baseUrl = 'http://localhost:8000';

function makeLoginRequest($username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => $response
    ];
}

// Test 1: Completely non-existent user
echo "1. Testing with completely non-existent username...\n";
$response = makeLoginRequest('nonexistent_user_' . time(), 'somepassword');
echo "Status: {$response['status']}\n";

if (strpos($response['body'], 'Invalid username or password') !== false) {
    echo "✅ Shows: 'Invalid username or password'\n";
} elseif (strpos($response['body'], 'User does not exist') !== false) {
    echo "❌ Shows: 'User does not exist'\n";
} elseif (strpos($response['body'], 'Username and password are required') !== false) {
    echo "⚠️  Shows: 'Username and password are required'\n";
} else {
    echo "? Shows other message. Checking response...\n";
    // Look for error messages in the HTML
    if (preg_match('/<p[^>]*>(.*?)<\/p>/s', $response['body'], $matches)) {
        echo "Error message found: " . strip_tags($matches[1]) . "\n";
    } else {
        echo "No clear error message found in response\n";
    }
}

echo "\n";

// Test 2: Existing user but wrong password
echo "2. Testing with existing username but wrong password...\n";
$response = makeLoginRequest('testuser', 'wrongpassword123');
echo "Status: {$response['status']}\n";

if (strpos($response['body'], 'Invalid username or password') !== false) {
    echo "✅ Shows: 'Invalid username or password'\n";
} elseif (strpos($response['body'], 'User does not exist') !== false) {
    echo "❌ Shows: 'User does not exist'\n";
} else {
    echo "? Shows other message. Checking response...\n";
    if (preg_match('/<p[^>]*>(.*?)<\/p>/s', $response['body'], $matches)) {
        echo "Error message found: " . strip_tags($matches[1]) . "\n";
    }
}

echo "\n";

// Test 3: Empty credentials
echo "3. Testing with empty credentials...\n";
$response = makeLoginRequest('', '');
echo "Status: {$response['status']}\n";

if (strpos($response['body'], 'Username and password are required') !== false) {
    echo "✅ Shows: 'Username and password are required'\n";
} else {
    echo "? Shows other message. Checking response...\n";
    if (preg_match('/<p[^>]*>(.*?)<\/p>/s', $response['body'], $matches)) {
        echo "Error message found: " . strip_tags($matches[1]) . "\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";

echo "\nSUMMARY:\n";
echo "- The system should show 'Invalid username or password' for both non-existent users and wrong passwords\n";
echo "- This is a security best practice (doesn't reveal if username exists)\n";
echo "- Empty credentials should show 'Username and password are required'\n";
