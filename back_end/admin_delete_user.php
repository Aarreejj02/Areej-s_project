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


$stmt = $conn->prepare("DELETE FROM users WHERE edit_code = ?");
$stmt->bind_param("s", $edit_code);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?msg=User+deleted+successfully");
} else {
    die("Error deleting user.");
}
?>
