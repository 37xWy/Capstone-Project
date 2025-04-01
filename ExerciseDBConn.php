<?php
$hostname = 'localhost'; 
$user = 'root';
$password = '';
$database  = 'healthytrack';

$connection = mysqli_connect($hostname, $user, $password, $database);

if ($connection === false) {
    die('Connection failed!' . mysqli_connect_error());
}

$sql = "SELECT * FROM exercise";
$result = $connection->query($sql);

$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = $row;
}

echo json_encode($exercises);
$connection->close();