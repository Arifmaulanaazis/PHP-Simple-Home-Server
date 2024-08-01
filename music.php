<?php
$file = isset($_GET['file']) ? urldecode($_GET['file']) : null;

if (!$file || !file_exists($file)) {
    die('File not found.');
}

$allowedExtensions = ['mp3', 'wav', 'ogg', 'flac'];
$fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    die('File format not supported.');
}

$mimeTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'flac' => 'audio/flac'
];
$mime = isset($mimeTypes[$fileExtension]) ? $mimeTypes[$fileExtension] : 'audio/mpeg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Music Player</h1>

    <div class="card">
        <div class="card-header">
            <span>Now Playing:</span>
            <span><?php echo htmlspecialchars(basename($file)); ?></span>
        </div>
        <div class="card-body">
            <audio controls style="width: 100%;">
                <source src="PM.php?file=<?php echo urlencode($file); ?>" type="<?php echo $mime; ?>">
                Your browser does not support the audio element.
            </audio>
            <a href="index.php?dir=<?php echo urlencode(dirname($file)); ?>" class="btn btn-primary mt-3">Kembali</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
