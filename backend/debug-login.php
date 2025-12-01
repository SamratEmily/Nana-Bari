<?php
echo "<h1>Login Debug Log</h1>";
echo "<p>Refresh this page after trying to login to see new logs.</p>";
echo "<a href='../login.html'>Go to Login Page</a> | <a href='debug-login.php?clear=1'>Clear Log</a><br><br>";

if (isset($_GET['clear'])) {
    file_put_contents('debug.log', '');
    header('Location: debug-login.php');
    exit;
}

$logFile = 'debug.log';

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (empty($logs)) {
        echo "<em>Log is empty. Try logging in first.</em>";
    } else {
        echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto;'>" . htmlspecialchars($logs) . "</pre>";
    }
} else {
    echo "<em>Debug log file does not exist yet. Try logging in first.</em>";
}
?>
