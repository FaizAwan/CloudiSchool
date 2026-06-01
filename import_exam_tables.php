<?php
/**
 * Import Exam Management Tables
 * Run this file to create the required exam management tables
 * Access via: http://localhost/commandarcityschool/import_exam_tables.php
 */

// Database configuration
$host = 'localhost';
$username = 'root';  // Default XAMPP MySQL username
$password = '';      // Default XAMPP MySQL password (empty)
$database = 'smartes_commandercityschool2'; // Your database name

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$database`");
    
    echo "<h2>Creating Exam Management Tables...</h2>\n";
    
    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/exam_management_tables.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception('SQL file not found: ' . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', preg_split('/;\s*$/m', $sql)),
        function($statement) {
            return !empty($statement) && !preg_match('/^--/', $statement);
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement)) || strpos(trim($statement), '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Show progress for table creation
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "<p style='color: green;'>✓ Created table: $tableName</p>\n";
                flush();
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            
            // Skip if table already exists
            if (strpos($e->getMessage(), 'already exists') !== false) {
                preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "<p style='color: orange;'>⚠ Table already exists: $tableName</p>\n";
                flush();
            } else {
                echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>\n";
                flush();
            }
        }
    }
    
    echo "<h3>Import Complete!</h3>";
    echo "<p><strong>Statements processed:</strong> " . count($statements) . "</p>";
    echo "<p><strong>Successful:</strong> $successCount</p>";
    echo "<p><strong>Errors:</strong> $errorCount</p>";
    
    // Test if question_bank table exists
    try {
        $result = $pdo->query("SHOW TABLES LIKE 'question_bank'");
        if ($result->rowCount() > 0) {
            echo "<p style='color: green; font-weight: bold;'>✓ question_bank table is ready!</p>";
            
            // Check table structure
            $columns = $pdo->query("DESCRIBE question_bank")->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Available columns:</strong> " . implode(', ', $columns) . "</p>";
            
        } else {
            echo "<p style='color: red; font-weight: bold;'>✗ question_bank table not found!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error checking question_bank table: " . $e->getMessage() . "</p>";
    }
    
    echo "<br><p><a href='question-bank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Question Bank →</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
