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
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p><strong>Success:</strong> <?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    
    <form method="post" action="/login">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required autofocus><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
        <button type="button" onclick="window.location.href='/register'">Register</button>
    </form>
</body>
</html>
