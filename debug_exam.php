<?php
/**
 * Debug Exam-related functionality
 * Access via: http://localhost/commandarcityschool/debug_exam.php
 */

echo "<h2>Debug Exam Functionality</h2>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "commandercityschool";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
} catch(PDOException $e) {
    die("<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>");
}

echo "<h3>1. Check Required Tables</h3>";
$required_tables = ['question_bank', 'exams', 'exam_questions', 'mcq_options', 'question_bank_options'];
$missing_tables = [];

foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } catch (PDOException $e) {
        $missing_tables[] = $table;
        echo "<p style='color: red;'>✗ Table '$table' missing</p>";
    }
}

if (!empty($missing_tables)) {
    echo "<p style='color: orange;'><strong>Run import_exam_tables.php to create missing tables.</strong></p>";
}

echo "<h3>2. Check Available Data</h3>";

// Check question banks
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM question_bank");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Question Banks: " . $result['count'] . " records</p>";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query("SELECT id, question_text, question_type, subject_id FROM question_bank LIMIT 3");
        echo "<h4>Sample Questions:</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Question Text</th><th>Type</th><th>Subject ID</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . $row['id'] . "</td><td>" . substr($row['question_text'], 0, 50) . "...</td><td>" . $row['question_type'] . "</td><td>" . $row['subject_id'] . "</td></tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error checking question banks: " . $e->getMessage() . "</p>";
}

// Check exams - first let's see what columns exist
try {
    $stmt = $pdo->query("DESCRIBE exams");
    echo "<h4>Exams Table Structure:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td><td>" . $row['Null'] . "</td><td>" . $row['Key'] . "</td></tr>";
    }
    echo "</table>";
    
    // Try to get exam data using the correct column name
    $stmt = $pdo->query("SELECT * FROM exams LIMIT 1");
    $exam_columns = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($exam_columns) {
        echo "<h4>Sample Exam Data:</h4>";
        echo "<pre>";
        print_r($exam_columns);
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error checking exams structure: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Test Add to Exam Functionality</h3>";

if ($_POST) {
    echo "<h4>Processing Add to Exam Request:</h4>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $question_bank_id = $_POST['question_bank_id'] ?? null;
    $exam_id = $_POST['exam_id'] ?? null;
    $marks = $_POST['marks'] ?? null;
    
    if ($question_bank_id && $exam_id && $marks) {
        try {
            // Check if question exists
            $stmt = $pdo->prepare("SELECT * FROM question_bank WHERE id = ?");
            $stmt->execute([$question_bank_id]);
            $bankQuestion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$bankQuestion) {
                echo "<p style='color: red;'>✗ Question bank ID $question_bank_id not found</p>";
            } else {
                echo "<p style='color: green;'>✓ Question bank found: " . substr($bankQuestion['question_text'], 0, 50) . "...</p>";
            }
            
            // Check if exam exists
            $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
            $stmt->execute([$exam_id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$exam) {
                echo "<p style='color: red;'>✗ Exam ID $exam_id not found</p>";
            } else {
                echo "<p style='color: green;'>✓ Exam found: " . $exam['exam_name'] . "</p>";
            }
            
            // Check for duplicates
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = ? AND question_bank_id = ?");
            $stmt->execute([$exam_id, $question_bank_id]);
            $duplicate = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($duplicate['count'] > 0) {
                echo "<p style='color: orange;'>⚠ Question already exists in this exam</p>";
            } else {
                echo "<p style='color: green;'>✓ No duplicate found</p>";
            }
            
            if ($bankQuestion && $exam && $duplicate['count'] == 0) {
                echo "<p style='color: green;'><strong>✓ All validation checks passed! Ready to add question to exam.</strong></p>";
            }
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Missing required fields: question_bank_id, exam_id, or marks</p>";
    }
}
?>

<h3>Test Form:</h3>
<form method="POST" style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
    <?php
    // Get available question banks
    try {
        $stmt = $pdo->query("SELECT id, question_text FROM question_bank LIMIT 10");
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div style='margin-bottom: 15px;'>";
        echo "<label>Question Bank ID:</label><br>";
        echo "<select name='question_bank_id' required>";
        echo "<option value=''>Select Question</option>";
        foreach ($questions as $q) {
            echo "<option value='" . $q['id'] . "'>" . $q['id'] . " - " . substr($q['question_text'], 0, 50) . "...</option>";
        }
        echo "</select>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Could not load questions: " . $e->getMessage() . "</p>";
    }
    
    // Get available exams
    try {
        $stmt = $pdo->query("SELECT id, exam_name FROM exams WHERE status = 'draft' LIMIT 10");
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div style='margin-bottom: 15px;'>";
        echo "<label>Exam ID:</label><br>";
        echo "<select name='exam_id' required>";
        echo "<option value=''>Select Exam</option>";
        foreach ($exams as $exam) {
            echo "<option value='" . $exam['id'] . "'>" . $exam['id'] . " - " . $exam['exam_name'] . "</option>";
        }
        echo "</select>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Could not load exams: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <div style="margin-bottom: 15px;">
        <label>Marks:</label><br>
        <input type="number" name="marks" value="5" min="1" max="100" required>
    </div>
    
    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Test Add to Exam</button>
</form>

<p><a href="question-bank" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Question Bank →</a></p>
