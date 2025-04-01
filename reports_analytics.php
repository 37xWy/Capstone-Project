<?php
include 'admin_auth.php';
include 'db.php';

$userActivityLabels = [];
$userActivityData   = [];
$queryActivity = "
    SELECT DATE(log_time) AS date, COUNT(*) AS active_users
    FROM activity_logs
    WHERE action = 'User logged in'
    GROUP BY DATE(log_time)
    ORDER BY DATE(log_time) ASC
";
$resultActivity = mysqli_query($conn, $queryActivity);
if ($resultActivity && mysqli_num_rows($resultActivity) > 0) {
    while ($row = mysqli_fetch_assoc($resultActivity)) {
        $userActivityLabels[] = $row['date'];
        $userActivityData[]   = (int)$row['active_users'];
    }
}

$topMealsLabels = [];
$topMealsData   = [];
$queryMeals = "
    SELECT 
      CASE 
        WHEN udl.diet_id IS NULL THEN 'Custom Made Plan'
        ELSE dp.plan_name
      END AS meal_plan_name,
      COUNT(*) AS popularity
    FROM user_diet_logs udl
    LEFT JOIN diet_plans dp ON udl.diet_id = dp.diet_id
    GROUP BY meal_plan_name
    ORDER BY popularity DESC
    LIMIT 5
";
$resultMeals = mysqli_query($conn, $queryMeals);
if ($resultMeals && mysqli_num_rows($resultMeals) > 0) {
    while ($row = mysqli_fetch_assoc($resultMeals)) {
        $topMealsLabels[] = $row['meal_plan_name'];
        $topMealsData[]   = (int)$row['popularity'];
    }
}

$queryExerciseSelection = "
    SELECT ex.E_name, COUNT(*) AS selection_count
    FROM exercise ex
    LEFT JOIN (
        SELECT E_id FROM cus_strengthworkout
        UNION ALL
        SELECT E_id FROM cus_cardioworkout
    ) AS workouts ON ex.E_id = workouts.E_id
    GROUP BY ex.E_id
    ORDER BY selection_count DESC
    LIMIT 5
";
$resultExerciseSelection = mysqli_query($conn, $queryExerciseSelection);
$exerciseLabels = [];
$exerciseData = [];
if ($resultExerciseSelection && mysqli_num_rows($resultExerciseSelection) > 0) {
    while ($row = mysqli_fetch_assoc($resultExerciseSelection)) {
        $exerciseLabels[] = $row['E_name'];
        $exerciseData[] = (int)$row['selection_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports & Analytics</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    .container {
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
    }
    h2 {
      margin-bottom: 10px;
      text-align: center;
    }
    p {
      margin-bottom: 20px;
      text-align: center;
    }
    .charts-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      margin: 20px auto;
      width: 100%;
    }
    .chart-placeholder {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
      width: 48%;
      height: 450px;
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }
    .chart-placeholder h3 {
      margin-bottom: 10px;
      font-size: 1.25rem;
      color: #333;
    }
    .chart-placeholder p {
      font-size: 0.95rem;
      color: #555;
      margin-bottom: 10px;
    }
    .chart-placeholder canvas {
      flex-grow: 1;
      width: 100%;
      max-height: 100%;
    }
    @media (max-width: 768px) {
      .chart-placeholder {
        width: 100%;
        height: auto;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <?php include 'header_sidebar.php'; ?>
    <div class="container">
      <h2><i class="fas fa-chart-pie"></i> Reports & Analytics</h2>
      <p>This page displays various charts and metrics including active user trends, popular meal plans, and exercise selections.</p>
      
      <div class="charts-row">
        <div class="chart-placeholder" id="chartUserActivity">
          <h3>User Activity Trend</h3>
          <p>Active users over time.</p>
          <canvas id="userActivityChart"></canvas>
        </div>
        <div class="chart-placeholder" id="chartTopMeals">
          <h3>Top Meal Plans</h3>
          <p>Popularity of top meal plans.</p>
          <canvas id="topMealsChart"></canvas>
        </div>
        <div class="chart-placeholder" id="chartExerciseSelection">
          <h3>Most Selected Exercise</h3>
          <p>Exercise selection frequency from workouts.</p>
          <canvas id="exerciseSelectionChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
    const userActivityChart = new Chart(userActivityCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($userActivityLabels); ?>,
        datasets: [{
          label: 'Active Users',
          data: <?php echo json_encode($userActivityData); ?>,
          backgroundColor: 'rgba(40, 167, 69, 0.2)',
          borderColor: 'rgba(40, 167, 69, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: {
              font: { size: 16 }
            }
          },
          tooltip: {
            bodyFont: { size: 14 },
            titleFont: { size: 16 }
          }
        },
        scales: {
          x: { 
            title: { display: true, text: 'Date', font: { size: 16 } },
            ticks: { font: { size: 14 } }
          },
          y: { 
            title: { display: true, text: 'Active Users', font: { size: 16 } },
            ticks: { font: { size: 14 } },
            beginAtZero: true
          }
        }
      }
    });
    
    const topMealsCtx = document.getElementById('topMealsChart').getContext('2d');
    const topMealsChart = new Chart(topMealsCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($topMealsLabels); ?>,
        datasets: [{
          label: 'Popularity',
          data: <?php echo json_encode($topMealsData); ?>,
          backgroundColor: '#1abc9c',
          borderColor: '#1abc9c',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { title: { display: true, text: 'Meal Plan' }, ticks: { font: { size: 14 } } },
          y: { title: { display: true, text: 'Popularity' }, ticks: { font: { size: 14 } }, beginAtZero: true }
        }
      }
    });
    
    const exerciseSelectionCtx = document.getElementById('exerciseSelectionChart').getContext('2d');
    const exerciseSelectionChart = new Chart(exerciseSelectionCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($exerciseLabels); ?>,
        datasets: [{
          label: 'Selection Count',
          data: <?php echo json_encode($exerciseData); ?>,
          backgroundColor: '#3498db',
          borderColor: '#3498db',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { title: { display: true, text: 'Exercise Name' }, ticks: { font: { size: 14 } } },
          y: { title: { display: true, text: 'Times Selected' }, ticks: { font: { size: 14 } }, beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>
