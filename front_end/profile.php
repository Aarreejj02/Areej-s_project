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


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $email = clean_input($_POST["email"]);
    $country = clean_input($_POST["country"]);
    $address = clean_input($_POST["address"]);
    $dob = clean_input($_POST["dob"]);
    $password = clean_input($_POST["password"]);

    if (!valid_email($email)) {
        $error = "Invalid email format";
    } elseif (!empty($password) && !valid_password($password)) {
        $error = "Password must be at least 8 characters with uppercase and number";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - AREEJ</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: #ffe6f2;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #d63384;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #d63384;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #b3246d;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
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
            <input type="text" name="country" value="<?= htmlspecialchars($user["country"] ?? '') ?>">

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
