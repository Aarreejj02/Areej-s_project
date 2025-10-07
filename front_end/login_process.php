<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        update_last_login($conn, $user["id"]);
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('Invalid credentials'); window.location.href='login.php';</script>";
    }
}
?>
