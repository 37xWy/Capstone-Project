<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: reset_password.html");
        exit();
    } else {
        $error = "Email not found. Please enter a registered email.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style-calvin.css">
</head>
<body>

    <div class="container">
        <div class="welcome-section">
            <h1>Forgot Your Password?</h1>
            <p>Enter your registered email to receive a password reset link.</p>
            <button class="register-btn" onclick="window.location.href='login.html'">Back to Login</button>
        </div>

        <div class="form-box">
            <form action="" method="POST">
                <h1>Forgot Password</h1>
                <p>Enter your email to reset your password</p>

                <div class="input-bot">
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?= $error; ?></p>
                <?php endif; ?>

                <button type="submit" class="btn">Reset Password</button>
            </form>
        </div>
    </div>

</body>
</html>
