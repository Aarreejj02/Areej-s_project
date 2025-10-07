<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";


if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}


$result = $conn->query("SELECT id, name, email, last_login, edit_code FROM users ORDER BY id DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - AREEJ</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 900px;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        table th {
            background: #d63384;
            color: white;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        a {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_create_user.php">Create User</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['last_login'] ?? 'Never' ?></td>
                            <td>
                               <a href='admin_edit_user.php?code=<?= htmlspecialchars($user['edit_code'] ?? '') ?>'>Edit</a> |
                               <a href='admin_delete_user.php?code=<?= htmlspecialchars($user['edit_code'] ?? '') ?>' onclick="return confirm('Are you sure?')">Delete</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
