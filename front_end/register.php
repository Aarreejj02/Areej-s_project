<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean_input($_POST["name"]);
    $user_number = clean_input($_POST["user_number"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

    // التحقق من البيانات
    if (!valid_email($email)) {
        $error = "❌ Invalid email format.";
    } elseif (!valid_password($password)) {
        $error = "❌ Password must be at least 8 characters, include uppercase and number.";
    } elseif (strlen($user_number) > 10) {
        $error = "❌ User number must be 10 characters or less.";
    } else {
        // كل شيء صحيح
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $edit_code = bin2hex(random_bytes(8));

        $stmt = $conn->prepare("INSERT INTO users (name, user_number, email, password, edit_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $user_number, $email, $hashed_password, $edit_code);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO user_profile (user_id, email) VALUES (?, ?)");
            $stmt2->bind_param("is", $user_id, $email);
            $stmt2->execute();

            $success = "✅ Registration successful! You can now log in.";
            // تفريغ الحقول بعد التسجيل
            $name = $user_number = $email = $password = "";
        } else {
            $error = "❌ Error inserting user: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: pink; 
        }
        .container {
            max-width: 450px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #d63384;
            margin-bottom: 25px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background-color: #d63384;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        button:hover {
            background-color: #b82a6c;
        }
        .error {
            color: red;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }
        .success {
            color: green;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Register</h1>

    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>

        <label for="user_number">User Number:</label>
        <input type="text" name="user_number" id="user_number" maxlength="10" value="<?= htmlspecialchars($user_number ?? '') ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

        <label>Password:</label>
        <input type="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" required>

        <button type="submit">Register</button>
    </form>

    <?php if(!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
</div>
</body>
</html>
