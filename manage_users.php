<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['status'])) {
    $userId = $_POST['user_id'];
    $status = $_POST['status'];

    if ($status !== "1" && $status !== "0") {
        echo "Invalid status value";
        $conn->close();
        exit();
    }

    $sql = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error: " . $conn->error;
        $conn->close();
        exit();
    }
    
    $stmt->bind_param("is", $status, $userId);

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
