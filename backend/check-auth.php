<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendResponse(false, 'Unauthorized', null);
}

sendResponse(true, 'Authenticated', ['email' => $_SESSION['user_email']]);
?>
