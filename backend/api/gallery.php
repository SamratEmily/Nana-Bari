<?php
require_once '../config.php';

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendResponse(false, 'Unauthorized');
}

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

// Increase limits for large image uploads
ini_set('memory_limit', '256M');
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
ini_set('max_execution_time', 300);

// Debug logging
function logDebug($message) {
    file_put_contents('../debug_gallery.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// GET - Fetch all gallery images
if ($method === 'GET') {
    $result = $conn->query("SELECT id, image_data, created_at FROM gallery ORDER BY created_at DESC");
    $images = [];
    
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => $row['id'],
            'image_data' => $row['image_data'], // Send base64 directly
            'created_at' => $row['created_at']
        ];
    }
    
    sendResponse(true, 'Images fetched successfully', $images);
}

// POST - Add new image
if ($method === 'POST') {
    $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
    logDebug("POST request received. Content-Length: " . $contentLength);

    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        $msg = 'Upload failed: Request body is empty.';
        if ($contentLength > 0) {
            $msg .= ' This usually means the file size exceeds the server limit (post_max_size).';
        }
        logDebug($msg);
        sendResponse(false, $msg);
    }

    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $msg = 'Invalid JSON data: ' . json_last_error_msg();
        logDebug($msg);
        sendResponse(false, $msg);
    }
    
    if (!isset($input['image_data'])) {
        logDebug('Image data missing in request');
        sendResponse(false, 'Image data is required');
    }
    
    $imageData = $input['image_data'];
    logDebug("Image data received. Length: " . strlen($imageData));
    
    // Validate base64 image
    if (!preg_match('/^data:image\/(\w+);base64,/', $imageData)) {
        logDebug('Invalid image format regex match failed');
        sendResponse(false, 'Invalid image format. Must be base64 encoded.');
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO gallery (image_data) VALUES (?)");
    
    if (!$stmt) {
        logDebug('Database prepare error: ' . $conn->error);
        sendResponse(false, 'Database prepare error: ' . $conn->error);
    }
    
    // Bind as string (LONGTEXT)
    $stmt->bind_param("s", $imageData);
    
    if ($stmt->execute()) {
        logDebug('Image uploaded successfully. ID: ' . $conn->insert_id);
        sendResponse(true, 'Image uploaded successfully', ['id' => $conn->insert_id]);
    } else {
        logDebug('Database execute error: ' . $stmt->error);
        // Check for packet size error
        if (strpos($stmt->error, 'max_allowed_packet') !== false) {
            sendResponse(false, 'Image is too large for the database. Please compress it further.');
        } else {
            sendResponse(false, 'Database error: ' . $stmt->error);
        }
    }
    
    $stmt->close();
}

// DELETE - Remove image
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        sendResponse(false, 'Image ID is required');
    }
    
    $id = intval($input['id']);
    
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Image deleted successfully');
    } else {
        sendResponse(false, 'Failed to delete image');
    }
}

$conn->close();
?>
