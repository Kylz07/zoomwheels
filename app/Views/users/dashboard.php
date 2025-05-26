<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Zoomwheels</title>
</head>
<body>
    <h1>🚗 Zoomwheels Dashboard</h1>
    
    <p><strong>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</strong></p>
    <p><a href="/logout">Logout</a></p>
    
    <hr>
      <h2>User Information</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
    
    <hr>
    
    <h2>Quick Actions</h2>
    <ul>
        <li><a href="/rentals">🚗 View Vehicle Rentals</a></li>
        <li><a href="/profile">👤 Edit Profile Settings</a></li>
        <li><a href="/history">📊 View Rental History</a></li>
        <li><a href="/billing">💳 Manage Billing</a></li>
    </ul>
    
    <hr>
    
    <h3>Getting Started</h3>
    <p>This is your central hub for managing all your vehicle rental needs. Explore the sections above to get started!</p>
    <p><em>More features will be available soon.</em></p>
</body>
</html>
