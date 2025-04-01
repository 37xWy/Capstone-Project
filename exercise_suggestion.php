<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthytrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlPlans = "SELECT plan_id, plan_name, description, difficulty, days, weeks FROM workout_plans";
$resultPlans = $conn->query($sqlPlans);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recommended Exercise - Healthy Track</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="boxes.css">
  <style>
    .exercise-container {
      max-width: 1000px;
      margin: auto;
      padding: 20px;
      text-align: center;
      background: #f4f4f4;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .search-filter-bar {
      margin: 20px auto;
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .search-filter-bar input, .search-filter-bar select, .search-filter-bar button {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .exercise-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .exercise {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .exercise img {
      max-width: 100%;
      border-radius: 10px;
      height: 180px;
      object-fit: cover;
    }
    #suggested-exercise img {
      width: 150px;
      height: 150px;
    }
    .disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    .workout-plans-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 40px;
    }
    .workout-plans-table th, .workout-plans-table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    .workout-plans-table th {
      background-color: #f2f2f2;
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

    function updateButtonState() {
      const goal = document.querySelector("#exercise-goal").value;
      const button = document.querySelector(".suggest-btn");
      button.disabled = goal === "all";
      button.classList.toggle("disabled", goal === "all");
    }

    function suggestExercise() {
      const goal = document.querySelector("#exercise-goal").value;
      let exercises = document.querySelectorAll(".exercise");
      let filteredExercises = [];

      if (goal === "burn-fat") {
        filteredExercises = Array.from(exercises).filter(exercise => 
          exercise.getAttribute("data-category") === "cardio" || 
          exercise.getAttribute("data-category") === "balance"
        );
      } else if (goal === "gain-muscle") {
        filteredExercises = Array.from(exercises).filter(exercise => 
          exercise.getAttribute("data-category") === "strength"
        );
      } else if (goal === "maintain-fitness") {
        filteredExercises = Array.from(exercises).filter(exercise => 
          exercise.getAttribute("data-category") === "flexibility" ||
          exercise.getAttribute("data-category") === "balance"
        );
      }

      if (filteredExercises.length === 0) {
        document.getElementById("suggested-exercise").innerHTML = "<p>No exercises available for this goal.</p>";
        return;
      }

      let randomIndex = Math.floor(Math.random() * filteredExercises.length);
      let exercise = filteredExercises[randomIndex];

      document.getElementById("suggested-exercise").innerHTML = `
          <h2>${exercise.querySelector('h2').textContent}</h2>
          <img src="${exercise.querySelector('img').src}" alt="${exercise.querySelector('h2').textContent}">
          <p>${exercise.querySelector('p').textContent}</p>
      `;
    }
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
          <li><a href="Exercise_Suggestion.php">Exercise Suggestion</a></li>
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

    <main>
      <div class="exercise-container">
        <h1>Recommended Exercises</h1>
        <div class="search-filter-bar">
          <select id="exercise-goal" onchange="updateButtonState()">
            <option value="all">Choose Goal</option>
            <option value="burn-fat">Burn Fat</option>
            <option value="gain-muscle">Gain Muscle</option>
            <option value="maintain-fitness">Maintain Fitness</option>
          </select>
          <button class="suggest-btn disabled" onclick="suggestExercise()" disabled>Suggest an Exercise</button>
        </div>
        <div id="suggested-exercise"></div>
        <div id="exercise-list" class="exercise-list">
          <div class="exercise" data-category="cardio">
            <h2>Running</h2>
            <img src="images/running.jpg" alt="Running">
            <p>A great cardio exercise that improves endurance and burns calories.</p>
          </div>
          <div class="exercise" data-category="cardio">
            <h2>Jump Rope</h2>
            <img src="images/jumprope.jpg" alt="Jump Rope">
            <p>Excellent for cardiovascular health and coordination.</p>
          </div>
          <div class="exercise" data-category="strength">
            <h2>Push-ups</h2>
            <img src="images/pushups.jpg" alt="Push-ups">
            <p>Builds upper body strength, targeting chest, shoulders, and arms.</p>
          </div>
          <div class="exercise" data-category="strength">
            <h2>Squats</h2>
            <img src="images/squats.jpg" alt="Squats">
            <p>Strengthens legs, glutes, and core muscles.</p>
          </div>
          <div class="exercise" data-category="strength">
            <h2>Bench Press</h2>
            <img src="images/benchpress.jpg" alt="Bench Press">
            <p>Great for upper body strength and muscle growth.</p>
          </div>
          <div class="exercise" data-category="flexibility">
            <h2>Yoga</h2>
            <img src="images/yoga.jpg" alt="Yoga">
            <p>Enhances flexibility, balance, and relaxation.</p>
          </div>
          <div class="exercise" data-category="balance">
            <h2>Plank</h2>
            <img src="images/plank.jpg" alt="Plank">
            <p>Great for core strength and balance.</p>
          </div>
          <div class="exercise" data-category="balance">
            <h2>Tai Chi</h2>
            <img src="images/taichi.jpg" alt="Tai Chi">
            <p>Improves balance, coordination, and mental relaxation.</p>
          </div>
        </div>
        <h2>Workout Plans &amp; Schedule</h2>
        <table class="workout-plans-table">
          <thead>
            <tr>
              <th>Plan Name</th>
              <th>Description</th>
              <th>Difficulty</th>
              <th>Days</th>
              <th>Weeks</th>
              <th>Schedule</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($resultPlans && $resultPlans->num_rows > 0) {
              while($plan = $resultPlans->fetch_assoc()) {
                $planId = $plan['plan_id'];
                $sqlSchedule = "SELECT wpe.day_of_week, e.E_name 
                                FROM workout_plan_exercises wpe 
                                JOIN exercise e ON wpe.E_id = e.E_id 
                                WHERE wpe.plan_id = '$planId'";
                $resultSchedule = $conn->query($sqlSchedule);
                $scheduleText = "";

                if ($resultSchedule && $resultSchedule->num_rows > 0) {
                    $scheduleByDay = [];
                    while($row = $resultSchedule->fetch_assoc()) {
                        $day = htmlspecialchars($row['day_of_week']);
                        $exercise = htmlspecialchars($row['E_name']);
                        if (!isset($scheduleByDay[$day])) {
                            $scheduleByDay[$day] = [];
                        }
                        $scheduleByDay[$day][] = $exercise;
                    }
                    
                    $scheduleParts = [];
                    foreach ($scheduleByDay as $day => $exercises) {
                        $scheduleParts[] = $day . ": " . implode(", ", $exercises);
                    }
                    $scheduleText = implode("; ", $scheduleParts);
                } else {
                    $scheduleText = "No exercises assigned.";
                }
                                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($plan['plan_name']) . "</td>";
                echo "<td>" . htmlspecialchars($plan['description']) . "</td>";
                echo "<td>" . htmlspecialchars($plan['difficulty']) . "</td>";
                echo "<td>" . htmlspecialchars($plan['days']) . "</td>";
                echo "<td>" . htmlspecialchars($plan['weeks']) . "</td>";
                echo "<td>" . $scheduleText . "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'>No workout plans available.</td></tr>";
            }
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <footer class="footer">
    Healthy Track Fitness Management System © 2025 | Designed for a Healthier You!
  </footer>
</body>
</html>
