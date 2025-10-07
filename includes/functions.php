<?php
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function valid_password($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}

function update_last_login($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>
