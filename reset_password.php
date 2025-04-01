<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "❌ Email not found. Please enter a registered email.";
        header("Location: reset_password.html");
        exit();
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "⚠️ Passwords do not match! Please check and try again.";
        header("Location: reset_password.html");
        exit();
    } else {
        $visible_password = $new_password; 

        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $visible_password, $email);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = "✅ Password changed successfully!";
            header("Location: password_changed.html");
            exit();
        } else {
            $_SESSION['error'] = "⚠️ Something went wrong. Please try again.";
            header("Location: reset_password.html");
            exit();
        }

        $update_stmt->close();
    }

    $stmt->close();
}

$conn->close();
?>

