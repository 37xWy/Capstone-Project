<?php
include 'admin_auth.php';
include 'db.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($type) {
        case 'diet':
            $id = trim($_POST['id']);
            $plan_name = trim($_POST['plan_name']);
            $description = trim($_POST['description']);
            $calories = intval($_POST['calories']);
            $diet_type = trim($_POST['diet_type']);
            
            $stmt = $conn->prepare("UPDATE diet_plans SET plan_name = ?, description = ?, calories = ?, diet_type = ? WHERE diet_id = ?");
            if ($stmt) {
                $stmt->bind_param("ssiss", $plan_name, $description, $calories, $diet_type, $id);
                if ($stmt->execute()) {
                    $message = "Diet plan updated successfully.";
                } else {
                    $message = "Error updating diet plan: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
            break;
        case 'exercise':
            $id = trim($_POST['id']);
            $exercise_name = trim($_POST['exercise_name']);
            $category = trim($_POST['category']);
            $target = trim($_POST['target']);
            $difficulty = trim($_POST['difficulty']);
            
            $stmt = $conn->prepare("UPDATE exercise SET E_name = ?, E_category = ?, E_target = ?, E_difficulty = ? WHERE E_id = ?");
            if ($stmt) {
                $stmt->bind_param("sssss", $exercise_name, $category, $target, $difficulty, $id);
                if ($stmt->execute()) {
                    $message = "Exercise updated successfully.";
                } else {
                    $message = "Error updating exercise: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
            break;
        case 'workout':
            $id = trim($_POST['id']);
            $plan_name = trim($_POST['plan_name']);
            $description = trim($_POST['description']);
            $days = intval($_POST['days']);
            $weeks = intval($_POST['weeks']);
            $difficulty = trim($_POST['difficulty']);
            
            $stmt = $conn->prepare("UPDATE workout_plans SET plan_name = ?, description = ?, days = ?, weeks = ?, difficulty = ? WHERE plan_id = ?");
            if ($stmt) {
                $stmt->bind_param("ssisss", $plan_name, $description, $days, $weeks, $difficulty, $id);
                if ($stmt->execute()) {
                    $message = "Workout plan updated successfully.";
                } else {
                    $message = "Error updating workout plan: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
            break;
        default:
            $message = "Invalid content type.";
            break;
    }
    $conn->close();
    echo $message;
    exit;
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}
?>
