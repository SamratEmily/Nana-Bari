<?php
require_once '../config.php';

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendResponse(false, 'Unauthorized');
}

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch all poems
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM kobita ORDER BY created_at DESC");
    $poems = [];
    
    while ($row = $result->fetch_assoc()) {
        $poems[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'body' => $row['body'],
            'when' => $row['created_at']
        ];
    }
    
    sendResponse(true, 'Poems fetched successfully', $poems);
}

// POST - Add new poem
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['body']) || trim($input['body']) === '') {
        sendResponse(false, 'Poem content is required');
    }
    
    $title = isset($input['title']) ? trim($input['title']) : 'নামহীন';
    $body = trim($input['body']);
    
    $stmt = $conn->prepare("INSERT INTO kobita (title, body) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $body);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Poem added successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Failed to add poem');
    }
}

// DELETE - Remove poem
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        sendResponse(false, 'Poem ID is required');
    }
    
    $id = intval($input['id']);
    
    $stmt = $conn->prepare("DELETE FROM kobita WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Poem deleted successfully');
    } else {
        sendResponse(false, 'Failed to delete poem');
    }
}

$conn->close();
?>
