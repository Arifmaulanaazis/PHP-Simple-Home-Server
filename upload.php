<?php
$targetDir = isset($_GET['dir']) ? urldecode($_GET['dir']) : 'C:/Users/arifm/Videos/JP';

if (!empty($_FILES['fileToUpload'])) {
    $targetFile = $targetDir . '/' . basename($_FILES['fileToUpload']['name']);
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
        echo "The file ". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "No file uploaded.";
}
?>
