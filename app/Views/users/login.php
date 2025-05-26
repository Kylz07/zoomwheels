<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zoomwheels</title>
</head>
<body>
    <h2>Login to Zoomwheels</h2>
    
    <?php if (!empty($error)): ?>
        <p><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p><strong>Success:</strong> <?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    
    <form method="post" action="/login">
        <p>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required autofocus>
        </p>
        
        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>
        
        <p>
            <button type="submit">Login</button>
        </p>
    </form>
    
    <p>Don't have an account? <a href="/register">Register here</a></p>
</body>
</html>
