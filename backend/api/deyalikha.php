<?php
require_once '../config.php';

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendResponse(false, 'Unauthorized');
}

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch all guestbook entries
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM deyalikha ORDER BY created_at DESC");
    $entries = [];
    
    while ($row = $result->fetch_assoc()) {
        $entries[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'msg' => $row['message'],
            'when' => $row['created_at']
        ];
    }
    
    sendResponse(true, 'Guestbook entries fetched successfully', $entries);
}

// POST - Add new guestbook entry
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['message']) || trim($input['message']) === '') {
        sendResponse(false, 'Message is required');
    }
    
    $name = isset($input['name']) && trim($input['name']) !== '' ? trim($input['name']) : 'অতিথি';
    $message = trim($input['message']);
    
    $stmt = $conn->prepare("INSERT INTO deyalikha (name, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $message);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Guestbook entry added successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Failed to add guestbook entry');
    }
}

// DELETE - Remove guestbook entry
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        sendResponse(false, 'Entry ID is required');
    }
    
    $id = intval($input['id']);
    
    $stmt = $conn->prepare("DELETE FROM deyalikha WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Guestbook entry deleted successfully');
    } else {
        sendResponse(false, 'Failed to delete guestbook entry');
    }
}

$conn->close();
?>
