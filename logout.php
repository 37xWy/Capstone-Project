<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $log_action = "User logged out";

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

    $log_sql = "INSERT INTO activity_logs (log_id, user_id, action) VALUES (?, ?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("sss", $new_log_id, $user_id, $log_action);
    $log_stmt->execute();
    $log_stmt->close();
}

session_unset();
session_destroy();

header("Location: login.html");
exit();
?>
