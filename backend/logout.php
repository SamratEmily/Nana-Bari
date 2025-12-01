<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$_SESSION = [];
session_destroy();

sendResponse(true, 'Logged out successfully');
?>
