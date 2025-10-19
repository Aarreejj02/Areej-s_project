
<?php
include "../includes/db.php";
include "../includes/functions.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = clean_input($_POST["email"]);

  
    $stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("❌ Email not found.");
    }

    $user = $result->fetch_assoc();

   
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abukishkareej2@gmail.com';   
        $mail->Password   = 'okfbszbuasxfrewd';            
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('abukishkareej2@gmail.com', 'AREEJ System');
        $mail->addAddress('abukishkareej2@gmail.com', 'Admin'); 

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
            <p>Hello Admin,</p>
            <p>User <strong>{$user['name']}</strong> (Email: {$email}) requested a password reset.</p>
            <p>Please go to your admin dashboard and send a new password.</p>
        ";

        $mail->send();
        echo "✅ Request sent successfully. Admin will reset your password soon.";
    } catch (Exception $e) {
        echo "❌ Error sending email: " . $mail->ErrorInfo;
    }
}
?>
