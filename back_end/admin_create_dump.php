<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$excelFile = realpath(__DIR__ . "/../uploads/users_dump.xlsx");

if (!file_exists($excelFile)) {
    die("<h2 style='text-align:center;color:red;'>❌ Excel file not found in: uploads/users_dump.xlsx</h2>");
}

try {
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $inserted = 0;
    $errors = [];

   
    for ($i = 1; $i < count($rows); $i++) {
        $name = trim($rows[$i][0] ?? '');
        $user_number = trim($rows[$i][1] ?? '');
        $email = trim($rows[$i][2] ?? '');

        if (empty($name) || empty($user_number) || empty($email)) {
            $errors[] = "Row $i contains incomplete data.";
            continue;
        }

      
        $plainPassword = substr(bin2hex(random_bytes(4)), 0, 8);
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        $edit_code = bin2hex(random_bytes(8));
        $created_at = date("Y-m-d H:i:s");

       
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Email $email already exists. Skipped.";
            continue;
        }

        
        $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password, created_at, edit_code, temp_password)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $user_number, $email, $hashedPassword, $created_at, $edit_code, $plainPassword);
        $stmt->execute();
        $inserted++;

       
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abukishkareej2@gmail.com';
            $mail->Password = 'okfbszbuasxfrewd';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('abukishkareej2@gmail.com', 'AREEJ Admin');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Your New Account Password";
            $mail->Body = "
                <div style='font-family:Arial,sans-serif;'>
                    <h3>Hello $name 👋</h3>
                    <p>Your account has been successfully created on the Areej system.</p>
                    <p><strong>Your new password:</strong> $plainPassword</p>
                    <p>Please make sure to change it after your first login.</p>
                    <br><small>Best regards,<br>AREEJ Team</small>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            $errors[] = "Email to $email could not be sent: {$mail->ErrorInfo}";
        }
    }

    echo "<div style='text-align:center;margin-top:50px;font-family:Arial;'>
            <h2 style='color:green;'>✅ $inserted new users have been successfully added!</h2>";

    if (!empty($errors)) {
        echo "<h3 style='color:red;'>⚠️ Notes:</h3><ul style='list-style:none;'>";
        foreach ($errors as $err) {
            echo "<li>$err</li>";
        }
        echo "</ul>";
    }

    echo "<br><a href='admin_dashboard.php' 
              style='background:#d63384;color:white;padding:10px 20px;border-radius:8px;
                     text-decoration:none;display:inline-block;'>Return to Dashboard</a>
          </div>";

} catch (Exception $e) {
    echo "<h2 style='color:red;text-align:center;'>Error reading Excel file: " . $e->getMessage() . "</h2>";
}
?>
