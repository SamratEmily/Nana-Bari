<?php
require_once '../config.php';

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendResponse(false, 'Unauthorized');
}

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch all awards
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM awards ORDER BY created_at DESC");
    $awards = [];
    
    while ($row = $result->fetch_assoc()) {
        $awards[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'desc' => $row['description'],
            'when' => $row['created_at']
        ];
    }
    
    sendResponse(true, 'Awards fetched successfully', $awards);
}

// POST - Add new award
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || trim($input['name']) === '') {
        sendResponse(false, 'Award name is required');
    }
    
    $name = trim($input['name']);
    $description = isset($input['desc']) ? trim($input['desc']) : '';
    
    $stmt = $conn->prepare("INSERT INTO awards (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Award added successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Failed to add award');
    }
}

// DELETE - Remove award
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        sendResponse(false, 'Award ID is required');
    }
    
    $id = intval($input['id']);
    
    $stmt = $conn->prepare("DELETE FROM awards WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Award deleted successfully');
    } else {
        sendResponse(false, 'Failed to delete award');
    }
}

$conn->close();
?>
