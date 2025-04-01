<?php
session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You are not logged in. Please log in first."]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_email"])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo json_encode(["success" => false, "message" => "Email cannot be empty."]);
        exit();
    }

    $query_check_email = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
    if ($stmt_check_email = $conn->prepare($query_check_email)) {
        $stmt_check_email->bind_param("ss", $email, $user_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();

        if ($stmt_check_email->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Email is already taken."]);
            exit();
        }
        $stmt_check_email->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database error while checking email availability."]);
        exit();
    }

    $query_update_email = "UPDATE users SET email = ? WHERE user_id = ?";
    if ($stmt_update_email = $conn->prepare($query_update_email)) {
        $stmt_update_email->bind_param("ss", $email, $user_id);
        if ($stmt_update_email->execute()) {
            echo json_encode(["success" => true, "message" => "Email updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update email."]);
        }
        $stmt_update_email->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database error while updating email."]);
    }

    $conn->close();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_password"])) {
    $new_password = trim($_POST['new-password']);
    $confirm_password = trim($_POST['confirm-password']);

    if (empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('Both password fields are required.'); window.location.href='accountsettings.html';</script>";
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href='accountsettings.html';</script>";
        exit();
    }

    $query_update_password = "UPDATE users SET password = ? WHERE user_id = ?";
    if ($stmt_update_password = $conn->prepare($query_update_password)) {
        $stmt_update_password->bind_param("ss", $new_password, $user_id);
        if ($stmt_update_password->execute()) {
            echo "<script>alert('Password updated successfully.'); window.location.href='accountsettings.html';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to update password.'); window.location.href='accountsettings.html';</script>";
            exit();
        }
        $stmt_update_password->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database error while updating password."]);
    }

    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim(htmlspecialchars($_POST['full-name']));
    $username = trim(htmlspecialchars($_POST['username']));
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);

    if (empty($full_name) || empty($username) || empty($dob) || empty($gender) || empty($height) || empty($weight)) {
        echo "<script>alert('All fields are required. Please fill out the form completely.'); window.history.back();</script>";
        exit();
    }

    $query_check_username = "SELECT 'user' AS type FROM users WHERE username = ? AND user_id != ? UNION SELECT 'admin' AS type FROM admins WHERE username = ? ";
    if ($stmt_check_username = $conn->prepare($query_check_username)) {
        $stmt_check_username->bind_param("sss", $username, $user_id, $username);
        $stmt_check_username->execute();
        $stmt_check_username->store_result();

        if ($stmt_check_username->num_rows > 0) {
            echo "<script>alert('Username is already taken. Please choose another one.'); window.history.back();</script>";
            exit();
        }
        $stmt_check_username->close();
    } else {
        echo "<script>alert('Database error while checking username availability.'); window.history.back();</script>";
        exit();
    }

    $query_update_info = "UPDATE users SET full_name = ?, username = ?, dob = ?, gender = ?, height = ?, weight = ? WHERE user_id = ?";
    if ($stmt_update_info = $conn->prepare($query_update_info)) {
        $stmt_update_info->bind_param("ssssdis", $full_name, $username, $dob, $gender, $height, $weight, $user_id);
        
        if ($stmt_update_info->execute()) {
            $_SESSION['username'] = $username;
            echo "<script>alert('Your personal information has been updated successfully!'); window.location.href='accountsettings.html';</script>";
            exit();
        } else {
            echo "<script>alert('Error updating information. Please try again.'); window.history.back();</script>";
            exit();
        }

        $stmt_update_info->close();
    } else {
        echo "<script>alert('Database error while updating personal information.'); window.history.back();</script>";
        exit();
    }

    $conn->close();
}
?>
