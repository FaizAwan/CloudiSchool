<?php
/**
 * Test Add Question to Exam functionality
 * Access via: http://localhost/commandarcityschool/test_add_to_exam.php
 */

echo "<h2>Test Add Question to Exam</h2>";

// Simulate the Laravel POST request data
$test_data = [
    'question_bank_id' => 1,  // We know this exists from debug_exam.php
    'exam_id' => 1,           // We know this exists from debug_exam.php  
    'marks' => 5
];

echo "<h3>Simulating Laravel Request:</h3>";
echo "<pre>";
print_r($test_data);
echo "</pre>";

// Test direct cURL to the Laravel route
echo "<h3>Testing via cURL:</h3>";

// Get the CSRF token first
echo "<h4>Step 1: Getting CSRF Token</h4>";
$login_url = 'http://localhost/commandarcityschool/login';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
$response = curl_exec($ch);

// Extract CSRF token from response
preg_match('/name="_token".*?value="([^"]*)"/', $response, $matches);
$csrf_token = $matches[1] ?? '';

if ($csrf_token) {
    echo "<p style='color: green;'>✓ CSRF token retrieved: " . substr($csrf_token, 0, 10) . "...</p>";
    
    // Now try to make the actual request to add-to-exam
    echo "<h4>Step 2: Testing Add to Exam Request</h4>";
    $add_to_exam_url = 'http://localhost/commandarcityschool/question-bank/add-to-exam';
    
    $post_data = array_merge($test_data, ['_token' => $csrf_token]);
    
    curl_setopt($ch, CURLOPT_URL, $add_to_exam_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "<p>HTTP Code: $http_code</p>";
    echo "<p>Response:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    if ($http_code == 200) {
        $json_response = json_decode($response, true);
        if ($json_response && isset($json_response['success'])) {
            echo "<p style='color: green;'>✓ SUCCESS: " . $json_response['message'] . "</p>";
        }
    } else if ($http_code == 422) {
        echo "<p style='color: orange;'>⚠ Validation errors found (this is expected if question already exists)</p>";
    } else if ($http_code == 500) {
        echo "<p style='color: red;'>✗ Server error occurred</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Could not retrieve CSRF token</p>";
}

curl_close($ch);

// Clean up cookie file
if (file_exists('cookie.txt')) {
    unlink('cookie.txt');
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If you see a 500 error, check <strong>storage/logs/laravel.log</strong> for the exact error</li>";
echo "<li>If you see validation errors, the form fields may not match what the controller expects</li>";
echo "<li>If you see success, the functionality is working correctly!</li>";
echo "</ol>";

echo "<p><a href='question-bank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to Question Bank →</a></p>";
echo "<p><a href='debug_exam.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Debug Info →</a></p>";
?>
