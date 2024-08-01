<?php
$file = isset($_GET['file']) ? urldecode($_GET['file']) : null;

if (!$file || !file_exists($file)) {
    die('File not found.');
}

$fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mimeTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'flac' => 'audio/flac'
];

if (!isset($mimeTypes[$fileExtension])) {
    die('File format not supported.');
}

$mime = $mimeTypes[$fileExtension];

header("Content-Type: $mime");
header('Content-Disposition: inline; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));

readfile($file);
?>
