<?php
    include("db.php");
    $stmt = $conn->prepare("UPDATE users SET status=0 WHERE user_id=?");
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute();
    $stmt->close();
    header("location:manage_users.php");
?> 