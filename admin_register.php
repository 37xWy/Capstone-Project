<?php
include 'admin_auth.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_query = "SELECT 'admin' AS type FROM admins WHERE email = ? OR username = ? 
                    UNION 
                    SELECT 'user' AS type FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ssss", $email, $username, $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: admin_register_fail.html");
        exit();
    }
    $stmt->close();

    $query = "SELECT admin_id FROM admins ORDER BY admin_id DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['admin_id'];
        $num = intval(substr($last_id, 1)) + 1;
        $new_admin_id = "A" . str_pad($num, 3, "0", STR_PAD_LEFT);
    } else {
        $new_admin_id = "A001";
    }

    $sql = "INSERT INTO admins (admin_id, full_name, email, username, password) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $new_admin_id, $full_name, $email, $username, $password);
        if ($stmt->execute()) {
            header("Location: login.html");
            exit();
        } else {
            header("Location: admin_register_fail.html");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: admin_register_fail.html");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style-calvin.css">
  <style>
    body {
      overflow-y: auto;
      scroll-behavior: smooth;
    }
    .scroll-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #007BFF;
      color: white;
      border: none;
      padding: 10px 15px;
      cursor: pointer;
      display: none;
    }
  </style>
</head>
<body>

  <button class="scroll-top" id="scrollTop">↑ Top</button>

  <div class="container">
    <div class="welcome-section">
      <h1>Admin Registration</h1>
      <p>Already have an account?</p>
      <button class="register-btn" onclick="window.location.href='login.html'">LOGIN</button>
    </div>

    <div class="form-box">
      <form action="admin_register.php" method="POST">
        <h1>Register</h1>

        <div class="input-bot">
          <input type="text" name="full_name" placeholder="Full Name" required>
          <i class='bx bxs-user'></i>
        </div>

        <div class="input-bot">
          <input type="email" name="email" placeholder="Email" required>
          <i class='bx bxs-envelope'></i>
        </div>

        <div class="input-bot">
          <input type="text" name="username" placeholder="Username" required>
          <i class='bx bxs-user-detail'></i>
        </div>

        <div class="input-bot">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <i class='bx bxs-lock'></i>
        </div>

        <button type="button" id="togglePassword" class="btn">Show Password</button>
        <button type="submit" class="btn">Register</button>
      </form>
    </div>
  </div>

  <script>
    document.getElementById("togglePassword").addEventListener("click", function() {
      let passwordField = document.getElementById("password");
      passwordField.type = (passwordField.type === "password") ? "text" : "password";
      this.textContent = (passwordField.type === "password") ? "Show Password" : "Hide Password";
    });

    window.addEventListener("scroll", function() {
      document.getElementById("scrollTop").style.display = window.scrollY > 200 ? "block" : "none";
    });

    document.getElementById("scrollTop").addEventListener("click", function() {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  </script>

</body>
</html>
