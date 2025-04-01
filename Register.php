<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $gender = $_POST['gender'];
    $fitness_level = $_POST['fitness_level'];
    $goal = $_POST['goal'] ?? NULL;
    $goal_value = !empty($_POST['goal_value']) ? $_POST['goal_value'] : NULL;
    $diet = !empty($_POST['diet']) ? $_POST['diet'] : NULL;

    if ($goal_value) {
        $goal_value = abs($goal_value);
    }

    $check_query = "SELECT 'user' AS type FROM users WHERE email = ? OR username = ? 
                    UNION 
                    SELECT 'admin' AS type FROM admins WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ssss", $email, $username, $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register_fail.html");
        exit();
    }
    $stmt->close();

    $query = "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['user_id'];
        $num = intval(substr($last_id, 1)) + 1;
        $new_user_id = "U" . str_pad($num, 3, "0", STR_PAD_LEFT);
    } else {
        $new_user_id = "U001";
    }

    $sql = "INSERT INTO users (user_id, full_name, email, username, password, dob, height, weight, gender, fitness_level, goal, goal_value, diet) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssiisssss", 
            $new_user_id, $full_name, $email, $username, $password, $dob, $height, $weight, $gender, 
            $fitness_level, $goal, $goal_value, $diet);

        if ($stmt->execute()) {
            header("Location: register_success.html");
            exit();
        } else {
            header("Location: register_fail.html");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: register_fail.html");
        exit();
    }
}

$conn->close();
?>
