<?php
include 'admin_auth.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = trim($_POST['plan_id']);
    $assignments_json = trim($_POST['assignments']);
    
    $assignments = json_decode($assignments_json, true);
    if (!is_array($assignments)) {
        echo "Invalid assignment data (JSON not array).";
        exit;
    }
    
    $delStmt = $conn->prepare("DELETE FROM workout_plan_exercises WHERE plan_id = ?");
    if (!$delStmt) {
        echo "Database error (delete step): " . $conn->error;
        exit;
    }
    $delStmt->bind_param("s", $plan_id);
    $delStmt->execute();
    $delStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO workout_plan_exercises (plan_id, E_id, day_of_week) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Database error (insert step): " . $conn->error;
        exit;
    }
    $conn->begin_transaction();
    
    $all_success = true;
    foreach ($assignments as $day => $exerciseIDs) {
        if (!is_array($exerciseIDs)) continue;
        foreach ($exerciseIDs as $eid) {
            $stmt->bind_param("sss", $plan_id, $eid, $day);
            if (!$stmt->execute()) {
                $all_success = false;
                break 2;
            }
        }
    }
    
    if ($all_success) {
        $conn->commit();
        echo "Assignments saved successfully.";
    } else {
        $conn->rollback();
        echo "Error saving assignments: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    exit;
} else {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}
?>
