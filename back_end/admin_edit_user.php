<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['code'])) {
    die("Invalid request.");
}

$edit_code = $_GET['code'];


$stmt = $conn->prepare("SELECT id, name, user_number, email FROM users WHERE edit_code = ?");
$stmt->bind_param("s", $edit_code);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $user_number = clean_input($_POST["user_number"]);
    $email = clean_input($_POST["email"]);

    if (!valid_email($email)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, user_number=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $user_number, $email, $user['id']);
        if ($stmt->execute()) {
            $success = "User updated successfully!";
            
            $user['name'] = $name;
            $user['user_number'] = $user_number;
            $user['email'] = $email;
        } else {
            $error = "Error updating user: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User - Admin Panel</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body { font-family: Arial, sans-serif; background: #f8f9fa; }
.container { max-width: 500px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
h1 { text-align: center; color: #d63384; }
label { font-weight: bold; }
input { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 5px; }
button { background: #d63384; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
button:hover { background: #b3246d; }
.error { color: red; margin-top: 10px; }
.success { color: green; margin-top: 10px; }
</style>
</head>
<body>
<div class="container">
    <h1>Edit User</h1>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <label>User Number:</label>
        <input type="text" name="user_number" value="<?= htmlspecialchars($user['user_number']) ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <button type="submit">Update User</button>
    </form>
    <div class="error"><?= $error ?></div>
    <div class="success"><?= $success ?></div>
    <a href="admin_dashboard.php" style="display:inline-block;margin-top:15px;color:#d63384;font-weight:bold;">Back to Dashboard</a>
</div>
</body>
</html>
