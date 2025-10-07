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
$error = "";
$success = "";


$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE edit_code = ?");
$stmt->bind_param("s", $edit_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$user_id = $user['id'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $email = clean_input($_POST["email"]);

    if (!valid_email($email)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        if ($stmt->execute()) {
            $success = "User updated successfully!";
            $user['name'] = $name;
            $user['email'] = $email;
        } else {
            $error = "Error updating user.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <button type="submit">Update</button>
        </form>
        <div class="error"><?= $error ?></div>
        <div class="success"><?= $success ?></div>
        <br>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
