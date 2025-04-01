<?php
include 'user_auth.php';
include 'role_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthy Track Fitness Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel ="stylesheet" href="boxes.css">
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
                    <li><a href="about_us.html">About us</About></a></li>
                    </a>
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
            <div class="large-box">
                <h2>Weight Progress:</h2>
           
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 50%;">50%</div>
                    </div>
                </div>
           
                <div class="weight-info">
                    <div class="weight-item">
                        <strong>Start:</strong>
                        <p id="start-weight">80 kg</p>
                    </div>
                    <div class="weight-item">
                        <strong>Current:</strong>
                        <p id="current-weight">65 kg</p>
                    </div>
                    <div class="weight-item">
                        <strong>Goal:</strong>
                        <p id="goal-weight">105 kg</p>
                    </div>
                </div>
            </div>                  
       
            <div class="row">
                <div class="small-box" style="text-align: center;">
                    <h2>Fitness Level</h2>
                    <p class="middle-text">Very Fit</p>
                </div>
                <div class="small-box" style="text-align: center;">
                    <h2>BMI</h2>
                    <p class="middle-text">20</p>
                </div>
 
                <div class="small-box" style="text-align: center;">
                    <h2>Calories Goal</h2>
                    <div class="calorie-tracker">
                        <p class="calories-count">2830</p>
                        <p class="calories-label">Goal to Reach</p>
                    </div>
                </div>
                                   
            </div>
       
            <div class="row">
            <div class="medium-box">
                <h2>Meal Plans:</h2>
                <ul id="meal-plan-list" style="font-size: 18px; margin-left: 20px; margin-top: 10px;"></ul>
            </div>
                <div class="medium-box">
                    <h2>Workout Goal:</h2>
                    <ul id="workout-goal-text" style="font-size: 18px; margin-left: 20px; margin-top: 10px;"></ul>
                    </div>
            </div>
       
<div class="large-box">
    <h2>Today's Activity:</h2>
 
    <div class="activity-container">
        <div class="activity-box">
            <h3>Steps</h3>
            <p>__ / day</p>
        </div>
        <div class="activity-box">
            <h3>Water Intake</h3>
            <p>__ / litre</p>
        </div>
        <div class="activity-box">
            <h3>Workout Done</h3>
            <p>__ / day</p>
        </div>
    </div>
</div>
 
    </div>

    <footer class="footer">
        Healthy Track Fitness Management System © 2025 | Designed for a Healthier You!
    </footer>
 
    <script>

function updateWorkoutGoal(startWeight, goalWeight) {
    let workoutGoalElement = document.getElementById("workout-goal-text");

    if (workoutGoalElement) {
        workoutGoalElement.innerHTML = "";

        let goal;
        if (startWeight > goalWeight) {
            goal = "lose_weight";
        } else if (startWeight < goalWeight) {
            goal = "gain_muscle";
        } else {
            goal = "maintain_weight";
        }

        let exercises = [];
        if (goal === "lose_weight") {
            exercises = ["Running", "Jump Rope", "Cycling"];
        } else if (goal === "gain_muscle") {
            exercises = ["Squats", "Deadlifts", "Bench Press"];
        } else if (goal === "maintain_weight") {
            exercises = ["Walking", "Cycling"];
        }

        exercises.forEach(exercise => {
            let li = document.createElement("li");
            li.textContent = exercise;
            workoutGoalElement.appendChild(li);
        });
    }
}

fetch("get_progress.php")
    .then(response => response.json())
    .then(data => {
        if (!data.error) {
            updateWorkoutGoal(data.start_weight, data.goal_weight);
        } else {
            console.error("Error fetching data:", data.error);
        }
    })
    .catch(error => console.error("Fetch error:", error));


updateWorkoutGoal("gain_muscle");


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

function updateProgress(startWeight, currentWeight, goalWeight) {
    let progressFill = document.querySelector(".progress-fill");

    let progress = ((currentWeight - startWeight) / (goalWeight - startWeight)) * 100;
    progress = Math.max(0, Math.min(progress, 100));

    progressFill.style.width = progress + "%";
    progressFill.textContent = Math.round(progress) + "%";

    document.getElementById("start-weight").textContent = startWeight + " kg";
    document.getElementById("current-weight").textContent = currentWeight + " kg";
    document.getElementById("goal-weight").textContent = goalWeight + " kg";
}

function fetchUserProgress() {
    fetch("get_progress.php")
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                updateProgress(data.start_weight, data.current_weight, data.goal_weight);
                updateActivity(data.steps, data.water_intake, data.workouts_done);
                updateFitnessLevel(data.fitness_level);
                updateBMI(data.bmi);
                updateCaloriesNeeded(data.calories_needed);
            } else {
                console.error("Error fetching data:", data.error);
            }
        })
        .catch(error => console.error("Fetch error:", error));
}

function updateBMI(bmi) {
    let bmiBox = document.querySelector(".small-box:nth-child(2) .middle-text");
    bmiBox.textContent = bmi !== null ? bmi : "N/A";
}

function updateFitnessLevel(fitnessLevel) {
    document.querySelector(".small-box:nth-child(1) .middle-text").textContent = fitnessLevel;
}

function updateCaloriesNeeded(calories) {
    let calorieBox = document.querySelector(".small-box:nth-child(3) .calories-count");
    
    if (calories !== null) {
        calorieBox.textContent = Math.round(calories);
    } else {
        calorieBox.textContent = "N/A";
    }
}

function updateActivity(steps, waterIntake, workoutsDone) {
    document.querySelector(".activity-box:nth-child(1) p").textContent = steps + " steps";
    document.querySelector(".activity-box:nth-child(2) p").textContent = waterIntake + " L";
    document.querySelector(".activity-box:nth-child(3) p").textContent = workoutsDone + " workouts";
}

function fetchMealPlan() {
    fetch('get_progress.php')
        .then(response => response.json())
        .then(data => {
            const mealPlanList = document.getElementById("meal-plan-list");

            if (data.meal_plan && Object.keys(data.meal_plan).length > 0) {
                mealPlanList.innerHTML = "";
                
                for (const [meal, item] of Object.entries(data.meal_plan)) {
                    let listItem = document.createElement("li");
                    listItem.innerHTML = `<strong>${meal}:</strong> ${item}`;
                    mealPlanList.appendChild(listItem);
                }
            } else {
                mealPlanList.innerHTML = "<li>No meal plan available.</li>";
            }
        })
        .catch(error => {
            console.error('Error fetching meal plan:', error);
            document.getElementById("meal-plan-list").innerHTML = "<li>Error loading meal plan.</li>";
        });
}

document.addEventListener("DOMContentLoaded", fetchMealPlan);
fetchUserProgress();
        
</script>
</body>
</html>
