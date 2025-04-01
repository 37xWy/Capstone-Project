<?php
include 'admin_auth.php';
include 'db.php';

header('Content-Type: application/json');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$type || !$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing type or id parameter"]);
    exit;
}

switch ($type) {
    case 'diet':
        $stmt = $conn->prepare("SELECT diet_id as id, plan_name, description, calories, diet_type FROM diet_plans WHERE diet_id = ?");
        break;
    case 'exercise':
        $stmt = $conn->prepare("SELECT E_id as id, E_name, E_category, E_target, E_difficulty FROM exercise WHERE E_id = ?");
        break;
    case 'workout':
        $stmt = $conn->prepare("SELECT plan_id as id, plan_name, description, days, weeks, difficulty FROM workout_plans WHERE plan_id = ?");
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid content type"]);
        exit;
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode($data);
exit;
?>
