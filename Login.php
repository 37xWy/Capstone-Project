<?php
session_start();
include 'db.php';

header("Content-Type: application/json");

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST['user_input']);
    $password = trim($_POST['password']);

    $sql = "SELECT user_id, username, email, password, status FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "redirect" => "login_fail.html"]);
        exit();
    }

    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['status'] != 1) {
            echo json_encode([
                "success" => false, 
                "error" => "Your account has been deactivated. Please contact support.",
                "redirect" => "account_deactivated.html"
            ]);
            exit();
        }

        if ($password === $user['password']) {  
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            $query = "SELECT log_id FROM activity_logs ORDER BY log_id DESC LIMIT 1";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_id = $row['log_id'];
                $num = intval(substr($last_id, 1)) + 1; 
                $new_log_id = "L" . str_pad($num, 3, "0", STR_PAD_LEFT);
            } else {
                $new_log_id = "L001"; 
            }

            $log_action = "User logged in";
            $log_sql = "INSERT INTO activity_logs (log_id, user_id, action) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            if ($log_stmt) {
                $log_stmt->bind_param("sss", $new_log_id, $user['user_id'], $log_action);
                $log_stmt->execute();
                $log_stmt->close();
            }

            echo json_encode([
                "success" => true,
                "user_id" => $user['user_id'],
                "username" => $user['username'],
                "redirect" => "capstonehomepage.php"
            ]);
            exit();
        } else {
            echo json_encode(["success" => false, "redirect" => "login_fail.html"]);
            exit();
        }
    }

    $stmt->close();

    $sql = "SELECT admin_id, username, email, password FROM admins WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "redirect" => "login_fail.html"]);
        exit();
    }

    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if ($password === $admin['password']) {  
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];

            echo json_encode([
                "success" => true,
                "admin_id" => $admin['admin_id'],
                "username" => $admin['username'],
                "redirect" => "admin_dashboard.php"
            ]);
            exit();
        } else {
            echo json_encode(["success" => false, "redirect" => "login_fail.html"]);
            exit();
        }
    }

    $stmt->close();

    echo json_encode(["success" => false, "redirect" => "login_fail.html"]);
    exit();
}

$conn->close();
?>
