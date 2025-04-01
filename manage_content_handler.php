<?php
include 'admin_auth.php';
include 'db.php';

function getNewID($conn, $table, $prefix, $column) {
    $query = "SELECT MAX(CAST(SUBSTRING($column, LENGTH(?) + 1) AS UNSIGNED)) AS max_id FROM $table WHERE $column LIKE ?";
    $like = $prefix . '%';
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $prefix, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $maxID = $row['max_id'];
    $newNum = $maxID ? $maxID + 1 : 1;
    $newID = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    $stmt->close();
    return $newID;
}

$content_type = isset($_GET['type']) ? $_GET['type'] : 'diet';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = trim($_POST['id']);
    
    switch ($content_type) {
        case 'diet':
            $stmt = $conn->prepare("DELETE FROM diet_plans WHERE diet_id = ?");
            break;
        case 'exercise':
            $stmt = $conn->prepare("DELETE FROM exercise WHERE E_id = ?");
            break;
        case 'workout':
            $stmt = $conn->prepare("DELETE FROM workout_plans WHERE plan_id = ?");
            break;
        default:
            $stmt = false;
            break;
    }
    
    if ($stmt) {
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $message = ucfirst($content_type) . " deleted successfully.";
        } else {
            $message = "Error deleting " . $content_type . ": " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Invalid content type or deletion error.";
    }
    
    $conn->close();
    header("Location: manage_content.php?type=" . $content_type . "&message=" . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($content_type) {
        case 'diet':
            $plan_name = trim($_POST['plan_name']);
            $description = trim($_POST['description']);
            $calories = intval($_POST['calories']);
            $diet_type = trim($_POST['diet_type']);
            
            $newID = getNewID($conn, 'diet_plans', 'DP', 'diet_id');
            
            $stmt = $conn->prepare("INSERT INTO diet_plans (diet_id, plan_name, description, calories, diet_type) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssis", $newID, $plan_name, $description, $calories, $diet_type);
                if ($stmt->execute()) {
                    $message = "Diet plan saved successfully with ID {$newID}.";
                } else {
                    $message = "Error saving diet plan: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
            break;

        case 'exercise':
            $exercise_name = trim($_POST['exercise_name']);
            $category = trim($_POST['category']);
            $target = trim($_POST['target']);
            $difficulty = trim($_POST['difficulty']);
            
            $newID = getNewID($conn, 'exercise', 'E', 'E_id');
            
            $stmt = $conn->prepare("INSERT INTO exercise (E_id, E_name, E_category, E_target, E_difficulty) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $newID, $exercise_name, $category, $target, $difficulty);
                if ($stmt->execute()) {
                    $message = "Exercise saved successfully with ID {$newID}.";
                } else {
                    $message = "Error saving exercise: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Database error: " . $conn->error;
            }
            break;

        case 'workout':
            $plan_name = trim($_POST['plan_name']);
            $description = trim($_POST['description']);
            $days = intval($_POST['days']);
            $weeks = intval($_POST['weeks']);
            $difficulty = trim($_POST['difficulty']);
            
            $newID = getNewID($conn, 'workout_plans', 'WP', 'plan_id');
            
            $stmt = $conn->prepare("INSERT INTO workout_plans (plan_id, plan_name, description, difficulty, days, weeks) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssii", $newID, $plan_name, $description, $difficulty, $days, $weeks);
                if ($stmt->execute()) {
                    $message = "Workout plan saved successfully with ID {$newID}.";
                } else {
                    $message = "Error saving workout plan: " . $stmt->error;
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
    header("Location: manage_content.php?type=" . $content_type . "&message=" . urlencode($message));
    exit;
} else {
    header("Location: manage_content.php?type=" . $content_type);
    exit;
}
?>
