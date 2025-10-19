<?php
include "../includes/db.php";
include "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $user_number = clean_input($_POST["user_number"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

  
    if (!valid_email($email)) {
        die("❌ Invalid email format.");
    }

   
    if (!valid_password($password)) {
        die("❌ Password must be at least 8 characters with uppercase and number.");
    }

   
    if (strlen($user_number) > 10) {
        die("❌ Error: User number must be 10 characters or less.");
    }

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

 
    $edit_code = bin2hex(random_bytes(8));

   
    $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password, edit_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $user_number, $email, $hashed_password, $edit_code);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

        
        $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, email) VALUES (?, ?)");
        $stmt2->bind_param("is", $user_id, $email);

        if (!$stmt2->execute()) {
            die("❌ Error inserting into user_profile: " . $stmt2->error);
        }

        
        header("Location: login.php");
        exit();
    } else {
        die("❌ Error inserting into users: " . $stmt->error);
    }
}
?>
