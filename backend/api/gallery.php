<?php
require_once 'config.php';

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
    $result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $images = [];
    
    while ($row = $result->fetch_assoc()) {
        // Check if it's a file path or base64 (legacy support)
        $src = $row['image_data'];
        if (strpos($src, 'uploads/') === 0) {
            // It's a file path, ensure we have the full URL
            // Assuming the API is in backend/api/, we need to go up two levels to root
            // But for the frontend, 'uploads/filename.jpg' is correct relative to index.html
            $src = $src; 
        }
        
        $images[] = [
            'id' => $row['id'],
            'image_data' => $src,
            'created_at' => $row['created_at']
        ];
    }
    
    sendResponse(true, 'Images fetched successfully', $images);
}

// POST - Add new image
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!isset($input['image_data'])) {
        sendResponse(false, 'Image data is required');
    }
    
    $imageData = $input['image_data'];
    
    // Check if it's a base64 string
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        $data = substr($imageData, strpos($imageData, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif
        
        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png', 'webp' ])) {
            sendResponse(false, 'Invalid image type');
        }
        
        $data = base64_decode($data);
        
        if ($data === false) {
            sendResponse(false, 'Base64 decode failed');
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $type;
        $filePath = '../../uploads/' . $filename; // Relative to backend/api/
        $dbPath = 'uploads/' . $filename; // Relative to index.html
        
        // Save to file
        if (file_put_contents($filePath, $data)) {
            // Save path to database
            $stmt = $conn->prepare("INSERT INTO gallery (image_data) VALUES (?)");
            $stmt->bind_param("s", $dbPath);
            
            if ($stmt->execute()) {
                sendResponse(true, 'Image uploaded successfully', ['id' => $conn->insert_id, 'path' => $dbPath]);
            } else {
                // Cleanup file if DB insert fails
                unlink($filePath);
                sendResponse(false, 'Database error: ' . $stmt->error);
            }
        } else {
            sendResponse(false, 'Failed to save file to disk');
        }
    } else {
        sendResponse(false, 'Invalid image data format');
    }
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
