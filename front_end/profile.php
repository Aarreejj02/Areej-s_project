<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$error = "";
$success = "";

// ✅ Check if admin sent new password
$stmtCheck = $conn->prepare("SELECT temp_password FROM users WHERE id = ?");
$stmtCheck->bind_param("i", $user_id);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
$temp = $resCheck->fetch_assoc();

if (!empty($temp["temp_password"])) {
    $new_pass_message = htmlspecialchars($temp["temp_password"]);

    echo "
    <div style='
        background:#d63384;
        color:white;
        padding:12px;
        border-radius:10px;
        text-align:center;
        margin:15px auto;
        max-width:500px;
        font-weight:bold;
        position:relative;
    '>
        🔔 Your password has been reset by the admin.<br>
        Your new password is:
        <span id='newPass' style=\"
            background:white;
            color:#d63384;
            padding:4px 8px;
            border-radius:6px;
            font-weight:bold;
            display:inline-block;
            margin-top:5px;
        \">$new_pass_message</span>
        <button onclick='copyPass()' style=\"
            background:white;
            color:#d63384;
            border:none;
            border-radius:6px;
            padding:4px 10px;
            margin-left:8px;
            cursor:pointer;
            font-weight:bold;
        \">📋 Copy</button>
        <div id='copyMsg' style='color:#ffe6f2;margin-top:5px;font-size:13px;'></div>
    </div>
    <script>
    function copyPass() {
        const pass = document.getElementById('newPass').innerText;
        navigator.clipboard.writeText(pass);
        document.getElementById('copyMsg').innerText = '✅ Password copied to clipboard';
        setTimeout(() => document.getElementById('copyMsg').innerText = '', 2000);
    }
    </script>
    ";

    // Delete temp password after showing it once
    $stmtDel = $conn->prepare("UPDATE users SET temp_password = NULL WHERE id = ?");
    $stmtDel->bind_param("i", $user_id);
    $stmtDel->execute();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $email = clean_input($_POST["email"]);
    $country = clean_input($_POST["country"]);
    $address = clean_input($_POST["address"]);
    $dob = clean_input($_POST["dob"]);
    $password = clean_input($_POST["password"]);

    if (!valid_email($email)) {
        $error = "Invalid email format.";
    } elseif (!empty($password) && !valid_password($password)) {
        $error = "Password must be at least 8 characters long, with one uppercase letter and a number.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
        }

        $stmt = $conn->prepare("SELECT profile_id FROM user_profile WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE user_profile SET country=?, address=?, date_of_birth=?, email=? WHERE user_id=?");
            $stmt->bind_param("ssssi", $country, $address, $dob, $email, $user_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO user_profile (user_id, country, address, date_of_birth, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $country, $address, $dob, $email);
            $stmt->execute();
        }

        $success = "Profile updated successfully!";
    }
}

$stmt = $conn->prepare("
    SELECT u.name, u.email, up.country, up.address, up.date_of_birth
    FROM users u
    LEFT JOIN user_profile up ON u.id = up.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// قائمة الدول للـ drop-down
$countries = [
    "United States", "Canada", "United Kingdom", "Australia", "Germany",
    "France", "Spain", "Italy", "Japan", "China", "India", "Other"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - AREEJ</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #ffe6f2; font-family: Arial, sans-serif; }
        .container { max-width: 500px; background: white; padding: 20px; margin: 50px auto; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #d63384; }
        label { font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #d63384; color: white; padding: 10px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        button:hover { background: #b3246d; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Update Profile</h1>
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required>

        <label>Country:</label>
        <select name="country">
            <option value="">Select Country</option>
            <?php foreach($countries as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= ($user["country"] ?? '') === $c ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Address:</label>
        <textarea name="address"><?= htmlspecialchars($user["address"] ?? '') ?></textarea>

        <label>Date of Birth:</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($user["date_of_birth"] ?? '') ?>" min="1910-01-01" max="2025-12-31">

        <label>New Password (leave blank if not changing):</label>
        <input type="password" name="password">

        <button type="submit">Update</button>
    </form>
    <div class="error"><?= $error ?></div>
    <div class="success"><?= $success ?></div>
</div>
</body>
</html>
