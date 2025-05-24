<!-- Registration View -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 400px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h2 { text-align: center; }
        .error { color: #b00; margin-bottom: 10px; }
        .success { color: #080; margin-bottom: 10px; }
        label { display: block; margin-top: 12px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 18px; width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
    <script>
        function validateForm() {
            let errors = [];
            const username = document.forms["registerForm"]["username"].value.trim();
            const email = document.forms["registerForm"]["email"].value.trim();
            const password = document.forms["registerForm"]["password"].value;
            const confirm = document.forms["registerForm"]["confirm_password"].value;
            if (!username) errors.push("Username is required.");
            if (!email) errors.push("Email is required.");
            else if (!/^\S+@\S+\.\S+$/.test(email)) errors.push("Invalid email format.");
            if (!password) errors.push("Password is required.");
            if (password.length < 6) errors.push("Password must be at least 6 characters.");
            if (password !== confirm) errors.push("Passwords do not match.");
            if (errors.length > 0) {
                document.getElementById("clientErrors").innerHTML = errors.join("<br>");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <div id="clientErrors" class="error"></div>
    <form name="registerForm" method="post" action="/register" onsubmit="return validateForm();">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
