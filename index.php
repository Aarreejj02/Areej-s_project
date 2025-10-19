
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AREEJ Website</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f6c1d2, #fadadd);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .navbar {
            background: #d63384;
            width: 100%;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            top: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-weight: bold;
            font-size: 18px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .welcome-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            text-align: center;
            width: 400px;
            margin-top: 100px;
            animation: fadeIn 1.2s ease-in-out;
        }

        h1 {
            color: #d63384;
            margin-bottom: 15px;
        }

        p {
            color: #555;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #d63384;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #b22b6c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="front_end/login.php">Login</a>
        <a href="front_end/register.php">Register</a>
        <a href="back_end/admin_login.php">Admin</a>
    </div>

    <div class="welcome-container">
        <h1>🌸 Welcome to AREEJ 🌸</h1>
        <p>Manage your profile, connect with users, and enjoy a beautiful design experience!</p>
        <a href="front_end/register.php" class="btn">Create an Account</a>
        <a href="front_end/login.php" class="btn">Login</a>
    </div>

</body>
</html>
