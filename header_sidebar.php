<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      background: linear-gradient(45deg, #2c3e50, #1abc9c);
      display: flex;
      align-items: center;
      padding: 0 10px;
      color: white;
      font-size: 20px;
      font-weight: bold;
      z-index: 900;
      justify-content: flex-start;
    }
    .header .left {
      margin-left: 15px;
    }
    .header .right {
      margin-left: auto;
      padding-right: 30px;
    }
    .menu-toggle {
      font-size: 24px;
      cursor: pointer;
      color: white;
      margin-right: 15px;
    }
    .auth-container {
      display: flex;
      align-items: center;
    }
    .auth-container a {
      display: block;
      text-decoration: none;
    }
    .auth-container img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
    }
    .auth-dropdown {
      position: absolute;
      top: 60px;
      right: 60px;
      background: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      display: none;
      min-width: 100px;
      z-index: 950;
    }
    .auth-dropdown a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #333;
      font-weight: bold;
      transition: background 0.3s;
    }
    .auth-dropdown a:hover {
      background: #f2f2f2;
    }
    .sidebar {
      position: fixed;
      top: 60px;
      bottom: 0;
      left: 0;
      width: 250px;
      background: linear-gradient(45deg, #2c3e50, #1abc9c);
      color: white;
      padding-top: 20px;
      overflow-y: auto;
      transition: width 0.3s ease-in-out;
      z-index: 800;
    }
    .sidebar.collapsed {
      width: 0;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .sidebar ul li a {
      display: block;
      padding: 15px;
      text-align: left;
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    .sidebar ul li a:hover {
      background: rgba(255,255,255,0.2);
    }
    .main-wrapper {
      margin-top: 60px;
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s ease-in-out;
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="left">
      <i class="fas fa-bars menu-toggle" onclick="toggleSidebar()"></i>
      <span>Admin Dashboard</span>
    </div>
    <div class="right">
      <div class="auth-container" id="authContainer">
        <a href="#" id="profile-btn">
          <img src="6522516.png" alt="Profile" id="authImage">
        </a>
        <div class="auth-dropdown" id="authDropdown">
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <div class="sidebar" id="sidebar">
    <ul>
      <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
      <li><a href="reports_analytics.php">Reports & Analytics</a></li>
      <li><a href="manage_content.php">Manage Content</a></li>
      <li><a href="user_management.php">User Management</a></li>
      <li><a href="user_logs_feedback.php">User Logs & Feedback</a></li>
      <li><a href="admin_register.php">Register New Admin</a></li>
    </ul>
  </div>

  <div class="main-wrapper" id="mainWrapper">
  
  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('sidebar');
      var mainWrapper = document.getElementById('mainWrapper');
      sidebar.classList.toggle('collapsed');
      if (sidebar.classList.contains('collapsed')) {
        mainWrapper.style.marginLeft = '0';
      } else {
        mainWrapper.style.marginLeft = '250px';
      }
    }

    document.getElementById('authContainer').addEventListener('click', function(e) {
      e.stopPropagation();
      var dropdown = document.getElementById('authDropdown');
      dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    });
    
    document.addEventListener('click', function() {
      document.getElementById('authDropdown').style.display = 'none';
    });
  </script>
  <script src="pageshow_reload.js"></script>
