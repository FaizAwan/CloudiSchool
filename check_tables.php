<?php
/**
 * Check database tables
 */

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "commandercityschool";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connected successfully\n\n";
    
    echo "Tables containing 'question':\n";
    $stmt = $pdo->query("SHOW TABLES LIKE '%question%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
    echo "\nTables containing 'exam':\n";
    $stmt = $pdo->query("SHOW TABLES LIKE '%exam%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
    echo "\nAll tables:\n";
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
