<?php
session_start();
include "../includes/db.php";
include "../includes/functions.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// تحديث أي مستخدم بدون edit_code لتوليد قيمة جديدة
$conn->query("UPDATE users SET edit_code = HEX(RANDOM_BYTES(8)) WHERE edit_code IS NULL OR edit_code = ''");

// جلب بيانات المستخدمين
$result = $conn->query("SELECT id, name, email, last_login, edit_code FROM users ORDER BY id ASC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - AREEJ</title>
    <link rel="stylesheet" href="../css/style.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f6c1d2, #fadadd);
            font-family: Arial, sans-serif;
        }
        .admin-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 95%;
            max-width: 1200px;
            margin: 20px auto;
        }
        .admin-actions {
            margin-bottom: 20px;
            text-align: center;
        }
        .admin-actions a {
            background: #d63384;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
            display: inline-block;
        }
        .admin-actions a:hover {
            background: #b3246d;
        }
        table.dataTable thead th {
            background: #d63384;
            color: white;
        }
        a.action-btn {
            color: #d63384;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
        }
        a.action-btn:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>

<div class="admin-container">
    <h1 style="text-align:center;">Admin Dashboard</h1>

    <div class="admin-actions">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_create_user.php">Create User</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <table id="usersTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php $counter = 1; ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['last_login'] ?? 'Never' ?></td>
                        <td>
                            <a class="action-btn" href='admin_edit_user.php?code=<?= urlencode($user['edit_code']) ?>'>Edit</a> |
                            <a class="action-btn" href='admin_delete_user.php?code=<?= urlencode($user['edit_code']) ?>' onclick="return confirm('Are you sure?')">Delete</a> |
                            <a class="action-btn" href='admin_send_password.php?code=<?= urlencode($user['edit_code']) ?>' onclick="return confirm('Send new password to this user?')">Send Password</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- JQuery + DataTables JS + Buttons -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Users Dashboard',
                text: 'Export to Excel'
            }
        ],
        columnDefs: [
            { targets: 4, orderable: false } 
        ]
        
    });
});
</script>

</body>
</html>
