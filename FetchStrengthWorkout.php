<?php
include 'db.php';

header("Content-Type: application/json"); 

if (isset($_GET['date']) && isset($_GET['user_id'])) {
    $date = $_GET['date'];
    $user_id = $_GET['user_id'];

    $sql = "SELECT sw.*, e.E_name 
            FROM cus_strengthworkout sw
            JOIN Exercise e ON sw.E_id = e.E_id
            WHERE sw.SW_date = ? AND sw.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $date, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $workouts = [];
    while ($row = $result->fetch_assoc()) {
        $workouts[] = $row;
    }

    echo json_encode($workouts);
} else {
    echo json_encode(["error" => "Invalid Request"]);
}

exit;
?>