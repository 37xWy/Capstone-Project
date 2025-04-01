<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['role'])) {
    $userId = $_POST['user_id'];
    $role = $_POST['role'];

    if ($role !== 'new' && $role !== 'long_time') {
        echo "Invalid role value";
        $conn->close();
        exit();
    }

    $sql = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error: " . $conn->error;
        $conn->close();
        exit();
    }
    $stmt->bind_param("ss", $role, $userId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    echo "Invalid request";
    exit();
}
?>
