<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = clean_input($_POST["email"]);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION["reset_email"] = $email;
        header("Location: admin_set_password.php");
        exit();
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Reset Password</h1>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Reset</button>
    </form>
    <div class="error"><?= $error ?></div>
</div>
</body>
</html>
