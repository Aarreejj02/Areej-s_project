<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: pink; 
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Register</h1>
    <form action="register_process.php" method="post">
        <label>Name:</label>
        <input type="text" name="name" required>
        
        <label for="user_number">User Number:</label>
        <input type="text" name="user_number" id="user_number" maxlength="8" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
