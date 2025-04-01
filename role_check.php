<?php
if (isset($_SESSION['user_id'])) {
    include 'db.php';

    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT role, created_at FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['role'] === 'new' && strtotime($user['created_at']) <= strtotime('-6 months')) {
            $updateQuery = "UPDATE users SET role = 'long_time' WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("s", $user_id);
            $updateStmt->execute();
            $updateStmt->close();
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
