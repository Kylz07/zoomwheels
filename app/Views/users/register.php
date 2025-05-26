<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zoomwheels</title>
</head>
<body>
    <h2>Register for Zoomwheels</h2>        
        <?php if (!empty($error)): ?>
            <p><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p><strong>Success:</strong> <?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <form method="post" action="/register">
            <p>
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required>
            </p>
            
            <p>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required>
            </p>
            
            <p>
                <label for="first_name">First Name:</label><br>
                <input type="text" id="first_name" name="first_name" required>
            </p>
            
            <p>
                <label for="last_name">Last Name:</label><br>
                <input type="text" id="last_name" name="last_name" required>
            </p>
            
            <p>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
            </p>
            
            <p>
                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </p>
            
            <p>
                <button type="submit">Register</button>
            </p>
        </form>
          <p>Already have an account? <a href="/login">Login here</a></p>
    </div>
</body>
</html>

