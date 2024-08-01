<?php
if (isset($_POST['filePath']) && isset($_POST['newName'])) {
    $filePath = urldecode($_POST['filePath']);
    $newName = $_POST['newName'];
    $newFilePath = dirname($filePath) . '/' . $newName;

    if (file_exists($filePath)) {
        if (rename($filePath, $newFilePath)) {
            echo 'File renamed successfully.';
        } else {
            http_response_code(500);
            echo 'Failed to rename file.';
        }
    } else {
        http_response_code(404);
        echo 'File not found.';
    }
} else {
    http_response_code(400);
    echo 'Invalid request.';
}
?>
