<?php
include 'admin_auth.php';
include 'db.php';

$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("SELECT full_name FROM admins WHERE admin_id = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $full_name = $admin['full_name'];
} else {
    echo "<p style='color:red; text-align:center; margin-top:20px;'>Admin not found. Please <a href='Login.html'>login</a> again.</p>";
    exit;
}
$stmt->close();

$most_active_stmt = $conn->prepare("
    SELECT u.full_name, COUNT(*) as count
    FROM activity_logs al
    JOIN users u ON al.user_id = u.user_id
    WHERE al.action LIKE '%logged in%'
    GROUP BY al.user_id
    ORDER BY count DESC
    LIMIT 1
");
$most_active_stmt->execute();
$most_active_result = $most_active_stmt->get_result();
if ($most_active_result->num_rows > 0) {
    $most_active_row = $most_active_result->fetch_assoc();
    $most_active_user = $most_active_row['full_name'];
} else {
    $most_active_user = 'N/A';
}
$most_active_stmt->close();

$most_logged_exercise_query = "
    SELECT E_name, SUM(cnt) as total FROM (
      SELECT e.E_name, COUNT(*) as cnt 
      FROM cus_cardioworkout c 
      JOIN exercise e ON c.E_id = e.E_id 
      GROUP BY c.E_id
      UNION ALL
      SELECT e.E_name, COUNT(*) as cnt 
      FROM cus_strengthworkout s 
      JOIN exercise e ON s.E_id = e.E_id 
      GROUP BY s.E_id
    ) as sub 
    GROUP BY E_name 
    ORDER BY total DESC 
    LIMIT 1
";
$most_logged_exercise_result = $conn->query($most_logged_exercise_query);
if ($most_logged_exercise_result && $most_logged_exercise_result->num_rows > 0) {
    $exercise_row = $most_logged_exercise_result->fetch_assoc();
    $most_logged_exercise = $exercise_row['E_name'];
} else {
    $most_logged_exercise = 'N/A';
}

$logins_week_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM activity_logs 
    WHERE action LIKE '%logged in%' 
    AND YEARWEEK(log_time, 1) = YEARWEEK(CURDATE(), 1)
");
$logins_week_stmt->execute();
$logins_week_result = $logins_week_stmt->get_result();
if ($logins_week_result->num_rows > 0) {
    $logins_week_row = $logins_week_result->fetch_assoc();
    $total_logins_week = $logins_week_row['total'];
} else {
    $total_logins_week = 0;
}
$logins_week_stmt->close();

$new_feedback_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM feedback 
    WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");
$new_feedback_stmt->execute();
$new_feedback_result = $new_feedback_stmt->get_result();
if ($new_feedback_result->num_rows > 0) {
    $feedback_row = $new_feedback_result->fetch_assoc();
    $new_feedback_total = $feedback_row['total'];
} else {
    $new_feedback_total = 0;
}
$new_feedback_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .content {
      flex: 1;
      padding: 20px;
      transition: all 0.3s ease-in-out;
      overflow-y: auto;
    }
    .card-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      border: 1px solid #ddd;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .stat-card i {
      font-size: 30px;
      color: #256F6E;
      margin-top: 20px;
      margin-bottom: 5px;
    }
    .stat-card h3 {
      font-size: 1.2rem;
      margin-bottom: 5px;
    }
    .stat-card p {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card i {
      font-size: 36px;
      color: #1DBFA1;
      margin-top: 10px;
      margin-bottom: 5px;
    }
    .card h3 {
      font-size: 1.2rem;
      margin-bottom: 10px;
    }
    .card p {
      font-size: 0.95rem;
      color: #555;
    }
  </style>
</head>
<body>
  <?php include 'header_sidebar.php'; ?>

  <div class="content" id="content">
    <h2>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</h2>
    <p>Here’s an overview of the system activity and key metrics.</p>
    
    <div class="stats-grid">
      <div class="stat-card">
        <i class="fas fa-users"></i>
        <h3>Most Active User</h3>
        <p><?php echo htmlspecialchars($most_active_user); ?></p>
      </div>
      <div class="stat-card">
        <i class="fas fa-dumbbell"></i>
        <h3>Most Logged Exercise</h3>
        <p><?php echo htmlspecialchars($most_logged_exercise); ?></p>
      </div>
      <div class="stat-card">
        <i class="fas fa-calendar-check"></i>
        <h3>Total Logins This Week</h3>
        <p><?php echo htmlspecialchars($total_logins_week); ?></p>
      </div>
      <div class="stat-card">
        <i class="fas fa-coins"></i>
        <h3>New Feedback Submitted</h3>
        <p><?php echo htmlspecialchars($new_feedback_total); ?></p>
      </div>
    </div>
    
    <p>Here’s a grid of function panels.</p>
    <div class="card-grid">
      <a href="reports_analytics.php" class="card-link">
        <div class="card">
          <i class="fas fa-chart-line"></i>
          <h3>Reports & Analytics</h3>
          <p>View activity logs, trends, and user insights.</p>
        </div>
      </a>
      <a href="manage_content.php" class="card-link">
        <div class="card">
          <i class="fas fa-edit"></i>
          <h3>Manage Content</h3>
          <p>Edit exercises and update diet plans.</p>
        </div>
      </a>
      <a href="user_management.php" class="card-link">
        <div class="card">
          <i class="fas fa-list-ul"></i>
          <h3>User Management</h3>
          <p>Manage user roles and accounts.</p>
        </div>
      </a>
      <a href="user_logs_feedback.php" class="card-link">
        <div class="card">
          <i class="fas fa-file-alt"></i>
          <h3>User Logs & Feedback</h3>
          <p>Review user logs and feedback.</p>
        </div>
      </a>
    </div>
  </div>
</body>
</html>
