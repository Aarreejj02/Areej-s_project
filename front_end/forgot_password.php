<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include "../includes/db.php";
include "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = trim($_POST["email"]);

   
    $stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
     
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'abukishkareej2@gmail.com';  
            $mail->Password   = 'okfbszbuasxfrewd';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

           
            $mail->setFrom('abukishkareej2@gmail.com', 'Areej System');
            $mail->addAddress('abukishkareej2@gmail.com', 'Admin');

           
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "
                <p>Hello Admin,</p>
                <p>The user <strong>{$user['name']}</strong> (<em>{$user_email}</em>) requested to reset their password.</p>
                <p>Please log in to your admin panel to send them a new password.</p>
            ";

            $mail->send();
            $success = "✅ Request sent! Admin will reset your password soon.";
        } catch (Exception $e) {
            $error = "❌ Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "❌ No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            margin: 100px auto;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        input[type="email"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background-color: #d63384;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #b3246d;
        }
        .success { color: green; margin-top: 15px; }
        .error { color: red; margin-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Forgot Password</h1>
    <form method="post">
        <label>Enter your registered email:</label><br>
        <input type="email" name="email" required><br>
        <button type="submit">Send Request</button>
    </form>

    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php elseif(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
</div>
</body>
</html>
