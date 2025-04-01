<?php
session_start();
include 'db.php';

function generateFeedbackID($conn) {
    $sql = "SELECT feedback_id FROM feedback WHERE feedback_id LIKE 'FB%' ORDER BY feedback_id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastNum = intval(substr($row['feedback_id'], 2));
        $newNum = $lastNum + 1;
    } else {
        $newNum = 1;
    }
    $newID = "FB" . str_pad($newNum, 3, "0", STR_PAD_LEFT);
    return $newID;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    $anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == '1';

    if ($anonymous || !isset($_SESSION['user_id'])) {
        $user_id = '0';
    } else {
        $user_id = $_SESSION['user_id'];
    }

    if (empty($message)) {
        die("Feedback message cannot be empty.");
    }

    $feedback_id = generateFeedbackID($conn);

    $stmt = $conn->prepare("INSERT INTO feedback (feedback_id, user_id, message, submitted_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("sss", $feedback_id, $user_id, $message);

    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    } else {
        header("Location: about_us.html");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
