<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 
include "../config_mail.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['code'])) {
    die("Invalid request.");
}

$edit_code = $_GET['code'];


$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE edit_code = ?");
$stmt->bind_param("s", $edit_code);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}


$newPassword = bin2hex(random_bytes(4)); 
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);


$update = $conn->prepare("UPDATE users SET password = ?, temp_password = ? WHERE id = ?");
$update->bind_param("ssi", $hashedPassword, $newPassword, $user['id']);

if ($update->execute()) {

    $mail = new PHPMailer(true);

    try {
        $mail->Host       = $MAIL_HOST;
        $mail->Username   = $MAIL_USERNAME;
        $mail->Password   = $MAIL_PASSWORD;
        $mail->setFrom($MAIL_FROM, $MAIL_FROM_NAME);

        $mail->setFrom('abukishkareej2@gmail.com', 'AREEJ Admin');
        $mail->addAddress($user['email'], $user['name']);

        $mail->isHTML(true);
        $mail->Subject = 'Your New Password';
        $mail->Body    = "
            <p>Hello <strong>{$user['name']}</strong>,</p>
            <p>Your password has been reset by the admin.</p>
            <p>Your new password is: <strong>{$newPassword}</strong></p>
            <p>Please change it after logging in.</p>
        ";

        $mail->send();
        $success = "✅ New password sent successfully to " . htmlspecialchars($user['email']) . ".";
    } catch (Exception $e) {
        $error = "❌ Mailer Error: " . $mail->ErrorInfo;
    }

} else {
    $error = "❌ Error updating password in database.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Send New Password - Admin Panel</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.container { max-width: 500px; background: white; padding: 30px; margin: 100px auto; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.1); text-align: center; }
.success { color: green; margin-top: 20px; }
.error { color: red; margin-top: 20px; }
a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #d63384; color: white; text-decoration: none; border-radius: 10px; font-weight: bold; }
a.btn:hover { background: #b3246d; }
</style>
</head>
<body>
<div class="container">
    <h1>Send New Password</h1>
    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php else: ?>
        <div class="error"><?= $error ?? "Unknown error." ?></div>
    <?php endif; ?>
    <a class="btn" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
