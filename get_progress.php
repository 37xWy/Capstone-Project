<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "SELECT weight, goal_value, fitness_level, height, dob, gender, diet FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit();
}

$user_data = $result->fetch_assoc();
$stmt->close();

$start_weight = (float) $user_data['weight'];
$goal_weight = (float) $user_data['goal_value'];
$fitness_level = (int) $user_data['fitness_level'];
$height_cm = (float) $user_data['height'];
$gender = strtolower(trim($user_data['gender']));
$diet = strtolower(trim($user_data['diet']));

$dob = $user_data['dob'];
$age = ($dob !== "0000-00-00") ? date_diff(date_create($dob), date_create('today'))->y : null;

$fitness_text = match ($fitness_level) {
    1 => "Unfit",
    3 => "Average",
    5 => "Super Fit",
    default => "Unknown"
};

$sql = "SELECT weight, steps, water FROM user_progress WHERE user_id = ? ORDER BY date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $progress_data = $result->fetch_assoc();
    $current_weight = (float) $progress_data['weight'];
    $steps = (int) $progress_data['steps'];
    $water_intake = (float) $progress_data['water'];
} else {
    $current_weight = $start_weight;
    $steps = 0;
    $water_intake = 0.0;
}
$stmt->close();

$bmi = ($height_cm > 0) ? round($current_weight / (($height_cm / 100) ** 2), 2) : null;

$workouts = ["cardio" => 0, "strength" => 0];

foreach (["cus_cardioworkout" => "cardio", "cus_strengthworkout" => "strength"] as $table => $type) {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $workouts[$type] = ($result->num_rows > 0) ? $result->fetch_assoc()['count'] : 0;
    $stmt->close();
}

if ($height_cm > 0 && $age !== null) {
    $bmr = ($gender === "male")
        ? (10 * $current_weight) + (6.25 * $height_cm) - (5 * $age) + 5
        : (10 * $current_weight) + (6.25 * $height_cm) - (5 * $age) - 161;

    $activity_factor = match ($fitness_level) {
        1 => 1.2,
        3 => 1.55,
        5 => 1.725,
        default => 1.2
    };

    $calories_needed = round($bmr * $activity_factor, 2);
} else {
    $calories_needed = null;
}

$weight_goal = ($goal_weight < $start_weight) ? "Weight Loss" : "Weight Gain";

$valid_diets = [
    "normal" => "normal",
    "vegan" => "vegan",
    "balanced" => "balanced diet",
    "balanced diet" => "balanced diet"
];
$diet = $valid_diets[$diet] ?? "normal";

$meal_plans = [
    "Weight Loss" => [
        "normal" => ["Breakfast" => "Oatmeal with Berries", "Lunch" => "Grilled Chicken Salad", "Dinner" => "Steamed Fish with Vegetables", "Snacks" => "Almonds and Green Tea"],
        "vegan" => ["Breakfast" => "Smoothie Bowl", "Lunch" => "Quinoa Salad", "Dinner" => "Lentil Soup", "Snacks" => "Hummus with Carrots"],
        "balanced diet" => ["Breakfast" => "Greek Yogurt with Nuts and Honey", "Lunch" => "Grilled Chicken with Quinoa", "Dinner" => "Baked Salmon with Brown Rice", "Snacks" => "Cottage Cheese with Fruits"]
    ],
    "Weight Gain" => [
        "normal" => ["Breakfast" => "Peanut Butter Toast with Banana", "Lunch" => "Beef Steak with Sweet Potatoes", "Dinner" => "Salmon with Pasta", "Snacks" => "Protein Shake with Nuts"],
        "vegan" => ["Breakfast" => "Peanut Butter Oats", "Lunch" => "Vegan Burrito", "Dinner" => "Tofu Stir-Fry", "Snacks" => "Avocado Toast"],
        "balanced diet" => ["Breakfast" => "Scrambled Eggs with Whole Grain Toast", "Lunch" => "Grilled Chicken with Quinoa", "Dinner" => "Baked Salmon with Brown Rice", "Snacks" => "Greek Yogurt with Granola"]
    ]
];

$selected_meals = $meal_plans[$weight_goal][$diet] ?? null;

echo json_encode([
    "start_weight" => $start_weight,
    "current_weight" => $current_weight,
    "goal_weight" => $goal_weight,
    "fitness_level" => $fitness_text,
    "bmi" => $bmi,
    "calories_needed" => $calories_needed,
    "steps" => $steps,
    "water_intake" => $water_intake,
    "total_cardio_workouts" => $workouts['cardio'],
    "total_strength_workouts" => $workouts['strength'],
    "workouts_done" => $workouts['cardio'] + $workouts['strength'],
    "meal_plan" => $selected_meals
]);

$conn->close();
?>
