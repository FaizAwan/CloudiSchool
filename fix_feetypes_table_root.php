<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'u296329189_cloudischoool';

$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Try the other database name
    $db = 'smartes_commandercityschool';
    $conn = @new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed with root: " . $conn->connect_error);
    }
}

echo "Connected successfully with root to $db\n";

// Add tenant_id if not exists
$result = $conn->query("SHOW COLUMNS FROM `feetypes` LIKE 'tenant_id'");
if ($result && $result->num_rows == 0) {
    echo "Adding tenant_id...\n";
    if ($conn->query("ALTER TABLE `feetypes` ADD `tenant_id` BIGINT(20) UNSIGNED NULL AFTER `id`")) {
        echo "tenant_id added\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else if ($result) {
    echo "tenant_id already exists\n";
} else {
    echo "Error querying columns: " . $conn->error . "\n";
}

// Add school_id if not exists
$result = $conn->query("SHOW COLUMNS FROM `feetypes` LIKE 'school_id'");
if ($result && $result->num_rows == 0) {
    echo "Adding school_id...\n";
    if ($conn->query("ALTER TABLE `feetypes` ADD `school_id` BIGINT(20) UNSIGNED NULL AFTER `tenant_id`")) {
        echo "school_id added\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else if ($result) {
    echo "school_id already exists\n";
}

$conn->close();
echo "Done\n";
