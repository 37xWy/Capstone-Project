<?php
include 'user_auth.php';
$user_id = $_SESSION['user_id'];

$selected_date = $_GET['date'] ?? date("Y-m-d");

include 'db.php';

$message = "";

$sql_diets = "SELECT diet_id, plan_name, description, calories, diet_type FROM diet_plans";
$result_diets = $conn->query($sql_diets);
$presetPlans = [];
if ($result_diets && $result_diets->num_rows > 0) {
    while($row = $result_diets->fetch_assoc()){
        $presetPlans[] = $row;
    }
}

$presetPlansJS = json_encode($presetPlans);

function generateNewLogId($conn) {
    $query = "SELECT MAX(CAST(SUBSTRING(log_id, 3) AS UNSIGNED)) AS max_num FROM user_diet_logs";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $maxNum = $row['max_num'];
        $newNum = ($maxNum !== null) ? $maxNum + 1 : 1;
        $new_log_id = "DL" . str_pad($newNum, 3, "0", STR_PAD_LEFT);
    } else {
        $new_log_id = "DL001";
    }
    return $new_log_id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diet_type = $_POST['diet_type'] ?? '';
    $selected_date = $_POST['selected_date'] ?? date("Y-m-d");
    $log_id = generateNewLogId($conn);
    
    if ($diet_type === 'preset') {
        $diet_id = $_POST['preset_diet'] ?? '';
        $meal_type = $_POST['preset_meal_type'] ?? '';
        $meal_description = "";
        $calories = intval($_POST['preset_calories'] ?? 0);
    } elseif ($diet_type === 'custom') {
        $diet_id = null;
        $meal_type = $_POST['custom_meal_type'] ?? '';
        $custom_plan_name = trim($_POST['custom_plan_name'] ?? '');
        $custom_plan_name = preg_replace("/[^A-Za-z0-9 ]/", "", $custom_plan_name);
        $custom_meal_desc = trim($_POST['custom_meal_desc'] ?? '');
        $calories = intval($_POST['custom_calories'] ?? 0);
        $meal_description = $custom_plan_name ? $custom_plan_name . ": " . $custom_meal_desc : $custom_meal_desc;
    }
    
    $stmt = $conn->prepare("INSERT INTO user_diet_logs (log_id, user_id, diet_id, meal_type, meal_description, calories, log_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $log_id, $user_id, $diet_id, $meal_type, $meal_description, $calories, $selected_date);
    if ($stmt->execute()) {

        $query = "SELECT log_id FROM activity_logs ORDER BY log_id DESC LIMIT 1";
        $result_log = $conn->query($query);
        if ($result_log->num_rows > 0) {
            $row_log = $result_log->fetch_assoc();
            $last_log_id = $row_log['log_id'];
            $num = intval(substr($last_log_id, 1)) + 1; 
            $new_log_id = "L" . str_pad($num, 3, "0", STR_PAD_LEFT);
        } else {
            $new_log_id = "L001";
        }

        $action = "User added a " . ($diet_type === 'preset' ? "Preset" : "Custom") . " meal log";

        $log_sql = "INSERT INTO activity_logs (log_id, user_id, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("sss", $new_log_id, $user_id, $action);
            $log_stmt->execute();
            $log_stmt->close();
        }
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?date=" . $selected_date);
        exit();
    } else {
        $message = "Error logging diet plan: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT ld.*, dp.plan_name, dp.description AS diet_description 
        FROM user_diet_logs ld 
        LEFT JOIN diet_plans dp ON ld.diet_id = dp.diet_id 
        WHERE ld.user_id = '$user_id' AND ld.log_date = '$selected_date'";
$result_logs = $conn->query($sql);

$meals = ["breakfast" => [], "lunch" => [], "dinner" => []];
$total_calories = ["breakfast" => 0, "lunch" => 0, "dinner" => 0];

if ($result_logs && $result_logs->num_rows > 0) {
    while ($row = $result_logs->fetch_assoc()){
        $type = $row['meal_type'];
        if (in_array($type, ["breakfast", "lunch", "dinner"])) {
            $meals[$type][] = $row;
            $total_calories[$type] += intval($row['calories']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Healthy Track - Diet Plan</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="boxes.css">
  <style>
    .day-switch {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
    }
    .day-switch button {
      padding: 10px;
      font-size: 18px;
      cursor: pointer;
    }
    .day-switch input[type="date"] {
      padding: 10px;
      font-size: 16px;
      border: 2px solid #0073e6;
      border-radius: 5px;
    }
    .meal-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      padding: 20px;
      max-width: 800px;
      margin: auto;
    }
    .meal-card {
      width: 100%;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .meal-header {
      background-color: #0073e6;
      color: white;
      padding: 15px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background-color 0.3s;
    }
    .meal-header:hover {
      background-color: #005bb5;
    }
    .meal-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.5s ease-in-out;
      background-color: #f9f9f9;
      padding: 0 15px;
    }
    .meal-content ul {
      list-style: none;
      padding: 10px 0;
      margin: 0;
    }
    .meal-content ul li {
      padding: 8px 0;
      font-size: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .meal-content ul li .meal-desc {
      flex: 1;
    }
    .meal-content ul li .meal-cal {
      margin-left: 10px;
      white-space: nowrap;
    }
    .total-calories {
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
      text-align: center;
    }
    .log-form {
      background: #f4f4f4;
      padding: 20px;
      margin-top: 30px;
      border-radius: 10px;
    }
    .log-form label {
      display: block;
      margin-top: 10px;
    }
    .log-form input[type="text"],
    .log-form input[type="number"],
    .log-form select,
    .log-form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .log-form input[type="radio"] {
      margin-right: 5px;
    }
    .log-form button {
      margin-top: 15px;
      padding: 10px 20px;
      border: none;
      background: #333;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }
    .message {
      padding: 10px;
      margin-bottom: 20px;
      background: #e0ffe0;
      border: 1px solid #b2ffb2;
      border-radius: 5px;
    }
    .total-calories {
        padding: 40px;
        margin: 0;
    }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const profileBtn = document.getElementById("profile-btn");
        const dropdownMenu = document.getElementById("dropdown-menu");

        profileBtn.addEventListener("click", function (event) {
            event.preventDefault();
            dropdownMenu.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            if (!profileBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove("show");
            }
        });
    });

    function changeDate(offset) {
      const dateInput = document.getElementById("selected-date");
      const currentDate = new Date(dateInput.value);
      currentDate.setDate(currentDate.getDate() + offset);
      const yyyy = currentDate.getFullYear();
      const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
      const dd = String(currentDate.getDate()).padStart(2, '0');
      const newDate = `${yyyy}-${mm}-${dd}`;
      dateInput.value = newDate;
      window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?date=" + newDate;
    }
    
    function toggleMeal(id) {
      var content = document.getElementById(id);
      if (content.style.maxHeight) {
          content.style.maxHeight = null;
      } else {
          content.style.maxHeight = content.scrollHeight + "px";
      }
    }
    
    function toggleDietOptions() {
        const dietType = document.querySelector('input[name="diet_type"]:checked').value;
        const presetElements = document.querySelectorAll("#preset-options input, #preset-options select");
        const customElements = document.querySelectorAll("#custom-options input, #custom-options select, #custom-options textarea");

        if (dietType === 'preset') {
            document.getElementById("preset-options").style.display = 'block';
            document.getElementById("custom-options").style.display = 'none';
            presetElements.forEach(el => { 
            el.disabled = false; 
            });
            customElements.forEach(el => { 
            el.disabled = true; 
            });
        } else {
            document.getElementById("preset-options").style.display = 'none';
            document.getElementById("custom-options").style.display = 'block';
            presetElements.forEach(el => { 
            el.disabled = true; 
            });
            customElements.forEach(el => { 
            el.disabled = false; 
            });
        }
    }

    const presetPlans = <?php echo $presetPlansJS; ?>;
    
    function updatePresetMealPlan() {
      const presetSelect = document.getElementById("preset_diet");
      const mealType = document.getElementById("preset_meal_type").value;
      const selectedPlan = presetPlans.find(plan => plan.diet_id === presetSelect.value);
      if (selectedPlan) {
        const mealDesc = selectedPlan.plan_name + ": " + selectedPlan.description;
        document.getElementById("preset_meal_desc").value = mealDesc;
        document.getElementById("preset_calories").value = selectedPlan.calories;
      } else {
        document.getElementById("preset_meal_desc").value = "";
        document.getElementById("preset_calories").value = 0;
      }
    }
    
    function updatePresetFields() {
      updatePresetMealPlan();
    }
    
    function calculateTotalCalories() {
        let total = 0;
        document.querySelectorAll('.meal-header span').forEach(span => {
            const calText = span.textContent.replace("kcal", "").trim();
            total += Number(calText) || 0;
        });
        return total;
    }
    
    function updateOverallTotal() {
        const overallTotal = calculateTotalCalories();
        const overallEl = document.getElementById("overall-total");
        if (overallEl) {
            overallEl.textContent = overallTotal + " kcal";
        }
    }
    
    window.onload = function() {
      toggleDietOptions();
      updatePresetMealPlan();
      updateOverallTotal();
    };
  </script>
</head>
<body>
    <div class="wrapper">
        <header class="header">
            <div class="logo">🏋️ Healthy Track</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="capstonehomepage.php">Homepage</a></li>
                    <li><a href="addinfo.html">Add Info</a></li>
                    <li><a href="diet_plan.php">Food</a></li>
                    <li><a href="Exercise.html">Exercise</a></li>
                    <li><a href="exercise_suggestion.php">Exercise Suggestion</a></li>
                    <li><a href="about_us.html">About us</a></li>
                </ul>
            </nav>
            <div class="profile-container">
                <a href="#" id="profile-btn">
                    <img src="Images/6522516.png" alt="Profile">
                </a>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="accountsettings.html">Account Settings</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        
        <div class="container">
            <?php if(!empty($message)) { echo "<div class='message'>" . htmlspecialchars($message) . "</div>"; } ?>
            
            <div class="day-switch">
                <button type="button" onclick="changeDate(-1)">&lt;</button>
                <input type="date" id="selected-date" name="selected_date" value="<?php echo $selected_date; ?>" onchange="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?date=' + this.value">
                <button type="button" onclick="changeDate(1)">&gt;</button>
            </div>
            
            <div class="meal-container">
                <div class="meal-card">
                    <div class="meal-header" onclick="toggleMeal('breakfast-card')">
                        Breakfast <span><?php echo $total_calories['breakfast']; ?> kcal</span>
                    </div>
                    <div id="breakfast-card" class="meal-content">
                        <ul>
                        <?php
                        if (!empty($meals['breakfast'])) {
                            foreach ($meals['breakfast'] as $log) {
                                if (!empty($log['diet_id'])) {
                                    $leftText = "<strong>" . htmlspecialchars($log['plan_name']) . "</strong>: " . htmlspecialchars($log['diet_description']);
                                } else {
                                    $leftText = htmlspecialchars($log['meal_description']);
                                }
                                echo "<li><span class='meal-desc'>" . $leftText . "</span><span class='meal-cal'>" . intval($log['calories']) . " kcal</span></li>";
                            }
                        } else {
                            echo "<li>No breakfast logged.</li>";
                        }
                        ?>
                        </ul>
                    </div>
                </div>
                
                <div class="meal-card">
                    <div class="meal-header" onclick="toggleMeal('lunch-card')">
                        Lunch <span><?php echo $total_calories['lunch']; ?> kcal</span>
                    </div>
                    <div id="lunch-card" class="meal-content">
                        <ul>
                        <?php
                        if (!empty($meals['lunch'])) {
                            foreach ($meals['lunch'] as $log) {
                                if (!empty($log['diet_id'])) {
                                    $leftText = "<strong>" . htmlspecialchars($log['plan_name']) . "</strong>: " . htmlspecialchars($log['diet_description']);
                                } else {
                                    $leftText = htmlspecialchars($log['meal_description']);
                                }
                                echo "<li><span class='meal-desc'>" . $leftText . "</span><span class='meal-cal'>" . intval($log['calories']) . " kcal</span></li>";
                            }
                        } else {
                            echo "<li>No lunch logged.</li>";
                        }
                        ?>
                        </ul>
                    </div>
                </div>
                
                <div class="meal-card">
                    <div class="meal-header" onclick="toggleMeal('dinner-card')">
                        Dinner <span><?php echo $total_calories['dinner']; ?> kcal</span>
                    </div>
                    <div id="dinner-card" class="meal-content">
                        <ul>
                        <?php
                        if (!empty($meals['dinner'])) {
                            foreach ($meals['dinner'] as $log) {
                                if (!empty($log['diet_id'])) {
                                    $leftText = "<strong>" . htmlspecialchars($log['plan_name']) . "</strong>: " . htmlspecialchars($log['diet_description']);
                                } else {
                                    $leftText = htmlspecialchars($log['meal_description']);
                                }
                                echo "<li><span class='meal-desc'>" . $leftText . "</span><span class='meal-cal'>" . intval($log['calories']) . " kcal</span></li>";
                            }
                        } else {
                            echo "<li>No dinner logged.</li>";
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <h2>Log a New Meal</h2>
            <form method="post" action="diet_plan.php" onsubmit="updatePresetFields()" class="log-form">
              <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
              
              <label>
                <input type="radio" name="diet_type" value="preset" onclick="toggleDietOptions()" checked>
                Preset Meal
              </label>
              <label>
                <input type="radio" name="diet_type" value="custom" onclick="toggleDietOptions()">
                Custom Meal
              </label>
              
              <div id="preset-options">
                <label for="preset_diet">Choose Preset Diet Plan:</label>
                <select name="preset_diet" id="preset_diet" onchange="updatePresetMealPlan()">
                  <?php
                  if (!empty($presetPlans)) {
                      foreach ($presetPlans as $plan) {
                          echo "<option value='" . htmlspecialchars($plan['diet_id']) . "'>" . htmlspecialchars($plan['plan_name']) . " (" . htmlspecialchars($plan['diet_type']) . ")</option>";
                      }
                  }
                  ?>
                </select>
                <label for="preset_meal_type">Meal Type:</label>
                <select name="preset_meal_type" id="preset_meal_type" onchange="updatePresetMealPlan()">
                  <option value="breakfast">Breakfast</option>
                  <option value="lunch">Lunch</option>
                  <option value="dinner">Dinner</option>
                </select>
                <input type="hidden" name="preset_meal_desc" id="preset_meal_desc">
                <input type="hidden" name="preset_calories" id="preset_calories">
              </div>
              
              <div id="custom-options" style="display:none;">
                <label for="custom_meal_type">Meal Type:</label>
                <select name="custom_meal_type" id="custom_meal_type">
                  <option value="breakfast">Breakfast</option>
                  <option value="lunch">Lunch</option>
                  <option value="dinner">Dinner</option>
                </select>
                <label for="custom_plan_name">Plan Name:</label>
                <input type="text" name="custom_plan_name" id="custom_plan_name" placeholder="Enter your meal plan name" pattern="[A-Za-z0-9 ]+" title="Only letters, numbers, and spaces allowed" required>
                <label for="custom_meal_desc">Meal Description:</label>
                <textarea name="custom_meal_desc" id="custom_meal_desc" rows="2" placeholder="Enter your meal description..." required></textarea>
                <label for="custom_calories">Calories:</label>
                <input type="number" name="custom_calories" id="custom_calories" placeholder="Enter calories" required>
              </div>
              
              <button type="submit">Log Meal</button>
            </form>
        </div>
    </div>
    <div class="total-calories">
        Overall Total Calories: <span id="overall-total"><?php echo array_sum($total_calories); ?> kcal</span>
    </div>
    <footer class="footer">
      Healthy Track Fitness Management System © 2025 | Designed for a Healthier You!
    </footer>
</body>
</html>
<?php
$conn->close();
?>
