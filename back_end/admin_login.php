<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = clean_input($_POST["username"]);
    $password = clean_input($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin["password"])) {
            $_SESSION["admin_id"] = $admin["id"];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials";
        }
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Admin Login</h1>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <div class="error"><?= $error ?></div>
</div>
</body>
</html>
