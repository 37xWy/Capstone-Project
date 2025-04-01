<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['admin_id'])) {
    echo "<p style='color:red; text-align:center; margin-top:20px;'>Warning: You must be logged in with admin credentials to access this page. Please <a href='Login.html'>login</a>.</p>";
    exit;
}
?>
