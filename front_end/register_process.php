<?php
include "../includes/db.php";
include "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $user_number = clean_input($_POST["user_number"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

   
    if (strlen($user_number) > 8) {
        die("Error: User number must be 8 characters or less.");
    }

    if (!valid_email($email)) {
        die("Invalid email format");
    } elseif (!valid_password($password)) {
        die("Password must be at least 8 characters with uppercase and number");
    }


    $hashed_user_number = password_hash($user_number, PASSWORD_DEFAULT);

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $hashed_user_number, $email, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, email) VALUES (?, ?)");
        $stmt2->bind_param("is", $user_id, $email);
        $stmt2->execute();

        header("Location: login.php");
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}
?>
