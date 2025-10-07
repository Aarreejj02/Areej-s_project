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
        $error = "Invalid email format";
    } elseif (!valid_password($password)) {
        $error = "Password must be at least 8 characters with uppercase and number";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $user_number, $email, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, email) VALUES (?, ?)");
            $stmt2->bind_param("is", $user_id, $email);
            $stmt2->execute();
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Error creating user: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Create User</h1>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>User Number:</label>
        <input type="text" name="user_number" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Create</button>
    </form>
    <div class="error"><?= $error ?></div>
</div>
</body>
</html>
