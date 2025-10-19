<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Areej</title>
    <link rel="stylesheet" href="../css/style.css">
    

</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form action="login_process.php" method="post">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
        </form>
    </div>
</body>
</html>
