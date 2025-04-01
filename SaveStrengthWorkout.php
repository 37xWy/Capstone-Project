<?php
session_start();
$hostname = 'localhost'; 
$user = 'root';
$password = '';
$database  = 'healthytrack';

$connection = mysqli_connect($hostname, $user, $password, $database);

if ($connection === false) {
    die(json_encode(["success" => false, "error" => "Database connection failed"]));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["strengthWorkouts"])) {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
    exit;
}

$exercises = $data["strengthWorkouts"];
$response = [];

foreach ($exercises as $exercise) {
    $E_name = $connection->real_escape_string($exercise["name"]);
    $weight = floatval($exercise["kg"]);
    $sets = intval($exercise["sets"]);
    $reps = intval($exercise["reps"]);
    $date = $connection->real_escape_string($exercise["date"]);

    $sql = "SELECT E_id FROM exercise WHERE E_name = '$E_name' LIMIT 1";
    $result = $connection->query($sql);
    if ($result->num_rows === 0) {
        $response[] = ["success" => false, "error" => "Exercise '$E_name' not found"];
        continue;
    }
    $E_id = $result->fetch_assoc()["E_id"];

    $sql = "SELECT SW_id FROM cus_strengthworkout ORDER BY SW_id DESC LIMIT 1";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = intval(substr($row["SW_id"], 2));
        $new_id = "SW" . str_pad($last_id + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $new_id = "SW001";
    }

    $sql = "INSERT INTO cus_strengthworkout (SW_id, E_id, SW_kg, SW_sets, SW_reps, SW_date, user_id) 
            VALUES ('$new_id', '$E_id', '$weight', '$sets', '$reps', '$date', '$user_id')";

    if ($connection->query($sql) === TRUE) {
        $query = "SELECT log_id FROM activity_logs ORDER BY log_id DESC LIMIT 1";
        $result_log = $connection->query($query);
        if ($result_log->num_rows > 0) {
            $row_log = $result_log->fetch_assoc();
            $last_log_id = $row_log['log_id'];
            $num = intval(substr($last_log_id, 1)) + 1; 
            $new_log_id = "L" . str_pad($num, 3, "0", STR_PAD_LEFT);
        } else {
            $new_log_id = "L001"; 
        }

        $log_action = "Logs doing $E_name";
        $log_sql = "INSERT INTO activity_logs (log_id, user_id, action) VALUES (?, ?, ?)";
        $log_stmt = $connection->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("sss", $new_log_id, $user_id, $log_action);
            $log_stmt->execute();
            $log_stmt->close();
        }

        $response[] = ["success" => true, "SW_id" => $new_id];
    } else {
        $response[] = ["success" => false, "error" => $connection->error];
    }
}

$connection->close();
echo json_encode(["success" => true, "data" => $response]);
?>
