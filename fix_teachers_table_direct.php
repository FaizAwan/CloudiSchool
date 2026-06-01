<?php
$host = '127.0.0.1';
$db = 'u296329189_cloudischoool';
$user = 'u296329189_cloudischoool';
$pass = 'Cloudischool@2026';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Checking 'teachers' table...\n";

// Check if status column exists
$result = $conn->query("SHOW COLUMNS FROM `teachers` LIKE 'status'");
if ($result->num_rows == 0) {
    echo "Adding 'status' column to 'teachers' table...\n";
    if ($conn->query("ALTER TABLE `teachers` ADD `status` VARCHAR(255) DEFAULT 'active' AFTER `id`")) {
        echo "Column 'status' added successfully.\n";
    } else {
        echo "Error adding 'status' column: " . $conn->error . "\n";
    }
} else {
    echo "Column 'status' already exists.\n";
}

// Check if teacherName column exists
$result = $conn->query("SHOW COLUMNS FROM `teachers` LIKE 'teacherName'");
if ($result->num_rows == 0) {
    echo "Adding 'teacherName' column to 'teachers' table...\n";
    if ($conn->query("ALTER TABLE `teachers` ADD `teacherName` VARCHAR(255) NULL AFTER `id`")) {
        echo "Column 'teacherName' added successfully.\n";
    } else {
        echo "Error adding 'teacherName' column: " . $conn->error . "\n";
    }
} else {
    echo "Column 'teacherName' already exists.\n";
}

$conn->close();
echo "Done.\n";
