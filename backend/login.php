<?php
require_once 'config.php';

// Debug logging function
function logDebug($message) {
    file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Log raw input
$rawInput = file_get_contents('php://input');
logDebug("Login attempt raw input: " . $rawInput);

$input = json_decode($rawInput, true);

if (!isset($input['email']) || !isset($input['password'])) {
    logDebug("Missing email or password");
    sendResponse(false, 'Email and password are required');
}

$email = trim($input['email']);
$password = trim($input['password']);

logDebug("Checking credentials for: " . $email);

// Get database connection
try {
    $conn = getDBConnection();
} catch (Exception $e) {
    logDebug("DB Connection failed: " . $e->getMessage());
    sendResponse(false, 'Database connection error');
}

// Check credentials from database
$stmt = $conn->prepare("SELECT id, email, name, password FROM users WHERE email = ?");
if (!$stmt) {
    logDebug("Prepare failed: " . $conn->error);
    sendResponse(false, 'Database error');
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    logDebug("User found: " . $user['email']);
    
    // Check password (direct comparison for now as requested)
    if ($password === $user['password']) {
        logDebug("Password match successful");
        
        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        
        $stmt->close();
        $conn->close();
        
        sendResponse(true, 'Login successful', [
            'email' => $user['email'],
            'name' => $user['name']
        ]);
    } else {
        logDebug("Password mismatch. Input: '$password', Stored: '" . $user['password'] . "'");
        $stmt->close();
        $conn->close();
        sendResponse(false, 'Invalid email or password');
    }
} else {
    logDebug("User not found: " . $email);
    $stmt->close();
    $conn->close();
    sendResponse(false, 'Invalid email or password');
}
?>
