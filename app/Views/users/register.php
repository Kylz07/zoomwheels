<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zoomwheels</title>
</head>
<body>
    <h2>Register</h2>        
        <?php if (!empty($error)): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <form method="post" action="/register">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit">Register</button>
            <button type="button" onclick="window.location.href='/login'">Login</button>
        </form>
          
    </div>
</body>
</html>

