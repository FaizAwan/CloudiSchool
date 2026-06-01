<?php
$conn = @new mysqli('127.0.0.1', 'root', '');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$res = $conn->query('SHOW DATABASES');
echo "Available databases:\n";
while($row = $res->fetch_assoc()) {
    echo $row['Database'] . "\n";
}
