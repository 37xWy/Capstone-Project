<?php
include 'admin_auth.php';
include 'db.php';

header('Content-Type: application/json');

if (isset($_GET['plan_id'])) {
    $plan_id = trim($_GET['plan_id']);
    
    $stmt = $conn->prepare("SELECT day_of_week, E_id FROM workout_plan_exercises WHERE plan_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $assignments = [];
    while ($row = $result->fetch_assoc()) {
        $day = $row['day_of_week'];
        if (!isset($assignments[$day])) {
            $assignments[$day] = [];
        }
        $assignments[$day][] = $row['E_id'];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($assignments);
    exit;
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing plan_id parameter"]);
    exit;
}
?>
