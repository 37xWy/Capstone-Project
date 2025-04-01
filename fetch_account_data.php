<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT user_id, full_name, username, dob, gender, height, weight, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if ($userData) {
    $userData['password'] = "*****";
}

echo json_encode($userData);
?>