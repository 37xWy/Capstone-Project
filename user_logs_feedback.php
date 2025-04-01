<?php
include 'admin_auth.php';
include 'db.php';

$logsQuery = "SELECT a.log_time, a.action, u.full_name AS username 
              FROM activity_logs a 
              LEFT JOIN users u ON a.user_id = u.user_id
              ORDER BY a.log_time DESC
              LIMIT 100";
$logsResult = $conn->query($logsQuery);

$feedbackQuery = "SELECT f.feedback_id, f.user_id, f.message, f.submitted_at, u.full_name AS username 
                  FROM feedback f 
                  LEFT JOIN users u ON f.user_id = u.user_id
                  ORDER BY f.submitted_at DESC 
                  LIMIT 50";
$feedbackResult = $conn->query($feedbackQuery);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Logs & Feedback</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-...your-integrity-key-here..."
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <style>
    .container {
      flex: 1;
      padding: 20px;
      transition: all 0.3s ease-in-out;
      overflow-y: auto;
    }
    .logs-feedback-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
    }
    .logs-card, .feedback-card {
      flex: 1;
      min-width: 300px;
      border: 1px solid #ddd;
      padding: 15px;
      background: #fff;
      border-radius: 4px;
    }
    .logs-container {
      max-height: 300px;
      overflow-y: auto;
      margin-top: 10px;
    }
    .logs-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }
    .logs-table th, .logs-table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    .logs-table th {
      background: #f2f2f2;
    }
    .logs-table tbody tr:nth-child(even) {
      background: #f9f9f9;
    }
    .logs-table tbody tr:hover {
      background: #e0f7fa;
    }
    .feedback-container {
      max-height: 300px;
      overflow-y: auto;
      margin-top: 10px;
      border: 1px solid #ddd;
      padding: 10px;
      background: #fff;
    }
    .feedback-list {
      list-style: disc;
      margin-left: 20px;
      padding: 0;
    }
    .feedback-list li {
      margin: 8px 0;
    }
    .filter-container {
      margin-top: 10px;
      margin-bottom: 10px;
    }
    .filter-container input, .filter-container select {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 0.9rem;
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <?php include 'header_sidebar.php'; ?>

  <div class="container">
    <h2><i class="fas fa-file-alt"></i> User Logs & Feedback</h2>
    <div class="filter-container">
      <input type="text" id="usernameFilter" placeholder="Filter by username..." onkeyup="filterLogs()">
      <select id="actionFilter" onchange="filterLogs()">
        <option value="all">All Actions</option>
        <option value="logged in">Login</option>
        <option value="logged out">Logout</option>
        <option value="Logs">Exercises</option>
        <option value="meal">Food</option>
      </select>
    </div>

    <div class="logs-feedback-container">
      <div class="logs-card">
        <i class="fas fa-clipboard-list"></i>
        <h3>User Logs</h3>
        <div class="logs-container">
          <table class="logs-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>User</th>
                <th>Log Message</th>
              </tr>
            </thead>
            <tbody id="logsTableBody">
              <?php
              if ($logsResult && $logsResult->num_rows > 0) {
                  while ($log = $logsResult->fetch_assoc()) {
                      $formattedDate = date("F j, Y, g:i A", strtotime($log['log_time']));
                      $username = !empty($log['username']) ? htmlspecialchars($log['username']) : "System";
                      $actionText = htmlspecialchars($log['action']);
                      echo "<tr>";
                      echo "<td>" . $formattedDate . "</td>";
                      echo "<td>" . $username . "</td>";
                      echo "<td>" . $actionText . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='3'>No logs found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="feedback-card">
        <i class="fas fa-comments"></i>
        <h3>User Feedback</h3>
        <div class="feedback-container">
          <ul class="feedback-list">
            <?php
            if ($feedbackResult && $feedbackResult->num_rows > 0) {
                while ($feedback = $feedbackResult->fetch_assoc()) {
                    $userDisplay = !empty($feedback["username"]) ? htmlspecialchars($feedback["username"]) : "Anonymous";
                    echo "<li>\"" . htmlspecialchars($feedback["message"]) . "\" - " . $userDisplay . "</li>";
                }
            } else {
                echo "<li>No feedback found.</li>";
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <script>
    function filterLogs() {
      const usernameFilter = document.getElementById("usernameFilter").value.toLowerCase();
      const actionFilter = document.getElementById("actionFilter").value.toLowerCase();
      const rows = document.querySelectorAll("#logsTableBody tr");
      rows.forEach(row => {
        const username = row.cells[1].textContent.toLowerCase();
        const actionText = row.cells[2].textContent.toLowerCase();
        let usernameMatch = username.includes(usernameFilter);
        let actionMatch = (actionFilter === "all") || actionText.includes(actionFilter);
        row.style.display = (usernameMatch && actionMatch) ? "" : "none";
      });
    }
  </script>
</body>
</html>
