<?php
/**
 * Simple database connectivity and table test
 * Access via: http://localhost/commandarcityschool/db_test.php
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'smartes_commandercityschool2';

echo "<h2>Database Connectivity Test</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Check if question_bank table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'question_bank'")->fetchAll();
    if (count($tables) > 0) {
        echo "<p style='color: green;'>✓ question_bank table exists!</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $pdo->query("DESCRIBE question_bank")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>✗ question_bank table does NOT exist!</p>";
        echo "<p>Please run the import script first: <a href='import_exam_tables.php'>Import Exam Tables</a></p>";
    }
    
    // Check if subjects table has data
    $subjects = $pdo->query("SELECT id, subject_name FROM subjects LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (count($subjects) > 0) {
        echo "<h3>Sample Subjects:</h3>";
        echo "<ul>";
        foreach ($subjects as $subject) {
            echo "<li>ID: {$subject['id']}, Name: {$subject['subject_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ No subjects found in database!</p>";
    }
    
    // Test insert permissions
    echo "<h3>Testing Insert Permissions:</h3>";
    try {
        $pdo->exec("CREATE TEMPORARY TABLE test_insert (id INT AUTO_INCREMENT PRIMARY KEY, test_field VARCHAR(50))");
        $pdo->exec("INSERT INTO test_insert (test_field) VALUES ('test')");
        $result = $pdo->query("SELECT * FROM test_insert")->fetch();
        if ($result) {
            echo "<p style='color: green;'>✓ Database insert permissions working!</p>";
        }
        $pdo->exec("DROP TEMPORARY TABLE test_insert");
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Insert permission test failed: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    
    echo "<h3>Common Solutions:</h3>";
    echo "<ul>";
    echo "<li>Check if XAMPP MySQL service is running</li>";
    echo "<li>Verify database name: $database</li>";
    echo "<li>Check MySQL username/password</li>";
    echo "<li>Ensure database exists</li>";
    echo "</ul>";
}

echo "<br><p><a href='question-bank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to Question Bank →</a></p>";
?>
