<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["admin_id"]) || !isset($_SESSION["reset_email"])) {
    header("Location: admin_login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = clean_input($_POST["password"]);
    $confirm_password = clean_input($_POST["confirm_password"]);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!valid_password($password)) {
        $error = "Password must be at least 8 characters with uppercase and number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $_SESSION["reset_email"]);

        if ($stmt->execute()) {
            unset($_SESSION["reset_email"]);
            $success = "Password updated successfully.";
        } else {
            $error = "Error updating password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Set New Password</h1>
    <form method="post">
        <label>New Password:</label>
        <input type="password" name="password" required>
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <button type="submit">Set Password</button>
    </form>
    <div class="error"><?= $error ?></div>
    <div class="success"><?= $success ?></div>
</div>
</body>
</html>
