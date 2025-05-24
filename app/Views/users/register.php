<!-- Registration View (PHP only, no CSS or JS) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
<div>
    <h2>Register</h2>
    <?php if (!empty($error)): ?>
        <div style="color: #b00; margin-bottom: 10px;"> <?php echo $error; ?> </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div style="color: #080; margin-bottom: 10px;"> <?php echo $success; ?> </div>
    <?php endif; ?>
    <form method="post" action="/register">
        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="first_name">First Name</label><br>
        <input type="text" id="first_name" name="first_name" required><br>
        <label for="last_name">Last Name</label><br>
        <input type="text" id="last_name" name="last_name" required><br>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm Password</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>

