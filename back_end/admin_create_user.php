<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $user_number = clean_input($_POST["user_number"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

    if (!valid_email($email)) {
        $error = "❌ Invalid email format.";
    } elseif (!valid_password($password)) {
        $error = "❌ Password must be at least 8 characters, include uppercase and number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $edit_code = bin2hex(random_bytes(8)); 

        $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password, edit_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $user_number, $email, $hashed_password, $edit_code);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, email) VALUES (?, ?)");
            $stmt2->bind_param("is", $user_id, $email);
            $stmt2->execute();

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "⚠️ Error creating user: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: "Segoe UI", sans-serif; 
        }
        .container { 
            background: #fff; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 0 15px rgba(0,0,0,0.1); 
            width: 450px; 
            margin: 60px auto; 
            text-align: center; 
        }
        h1 { 
            text-align: center; 
            color: #d63384; 
            margin-bottom: 25px; 
        }
        form { 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }
        label { 
            font-weight: bold; 
            color: #555; 
        }
        input { 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-size: 14px; 
        }
        button { 
            background-color: #d63384; 
            color: white; 
            border: none; 
            padding: 10px; 
            border-radius: 8px; 
            font-size: 16px; 
            cursor: pointer; 
            transition: 0.2s; 
        }
        button:hover { 
            background-color: #b82a6c; 
        }
        .error { 
            color: red; 
            margin-top: 15px; 
            font-weight: bold; 
        }

        .navbar {
            margin-top: 20px;
            display: flex;
            justify-content: center; 
            gap: 15px; 
        }

        .navbar a {
            background: #d63384;
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .navbar a:hover {
            background: #b3246d;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create New User</h1>

    
    <div class="navbar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_create_user.php">Create User</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>User Number:</label>
        <input type="text" name="user_number" maxlength="10" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Create User</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

</body>
</html>
