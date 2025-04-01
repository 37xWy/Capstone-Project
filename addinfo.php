<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "You are not logged in."]);
        exit();
    }

    $user_id = trim($_SESSION['user_id']); 

    if (!preg_match("/^U\d+$/", $user_id)) {
        echo json_encode(["success" => false, "message" => "Invalid User ID format."]);
        exit();
    }

    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $steps = isset($_POST['steps']) ? intval($_POST['steps']) : 0;
    $water = isset($_POST['water']) ? floatval($_POST['water']) : 0;
    $current_date = date('Y-m-d');

    if ($weight <= 0 || $steps < 0 || $water <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid input. Please enter valid values."]);
        exit();
    }

    $query = "INSERT INTO user_progress (user_id, weight, steps, water, date) 
              VALUES (?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE 
              weight = VALUES(weight), 
              steps = VALUES(steps), 
              water = VALUES(water), 
              date = VALUES(date)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sidds", $user_id, $weight, $steps, $water, $current_date);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Progress updated successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update progress. Please try again."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database error. Please try again later."]);
    }

    $conn->close();
}
?>
