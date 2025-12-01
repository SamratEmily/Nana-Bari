<?php
echo "<h1>Gallery Debug Log</h1>";
echo "<p>Refresh this page after trying to upload an image.</p>";
echo "<a href='../index.html'>Go Back</a> | <a href='debug-gallery.php?clear=1'>Clear Log</a><br><br>";

if (isset($_GET['clear'])) {
    file_put_contents('../debug_gallery.log', '');
    header('Location: debug-gallery.php');
    exit;
}

$logFile = '../debug_gallery.log';

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (empty($logs)) {
        echo "<em>Log is empty. Try uploading an image first.</em>";
    } else {
        echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto;'>" . htmlspecialchars($logs) . "</pre>";
    }
} else {
    echo "<em>Debug log file does not exist yet. Try uploading an image first.</em>";
}
?>
