<?php
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    error_log("Requested file: " . $file);

    if (file_exists($file)) {
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        $mimeType = '';

        switch ($fileExtension) {
            case 'mp4':
                $mimeType = 'video/mp4';
                break;
            case 'avi':
                $mimeType = 'video/x-msvideo';
                break;
            case 'mov':
                $mimeType = 'video/quicktime';
                break;
            case 'wmv':
                $mimeType = 'video/x-ms-wmv';
                break;
            case 'flv':
                $mimeType = 'video/x-flv';
                break;
            case 'ts':
                $mimeType = 'video/MP2T';
                break;
            case 'mkv':
                $mimeType = 'video/x-matroska';
                break;
            default:
                echo "Unsupported video format.";
                exit;
        }

        if (isset($_GET['stream'])) {
            if (file_exists($file)) {
                $mimeType = mime_content_type($file);

                header('Content-Type: ' . $mimeType);
                header('Accept-Ranges: bytes');

                $fileSize = filesize($file);
                $start = 0;
                $end = $fileSize - 1;

                if (isset($_SERVER['HTTP_RANGE'])) {
                    $range = $_SERVER['HTTP_RANGE'];
                    $range = str_replace('bytes=', '', $range);
                    $range = explode('-', $range);
                    $start = intval($range[0]);
                    if (isset($range[1]) && $range[1] !== '') {
                        $end = intval($range[1]);
                    }
        
                    header('HTTP/1.1 206 Partial Content');
                }

                header("Content-Range: bytes $start-$end/$fileSize");
                header("Content-Length: " . ($end - $start + 1));

                $file = fopen($file, 'rb');
                fseek($file, $start);
                $bufferSize = 1024 * 8;
                while (!feof($file) && ($pos = ftell($file)) <= $end) {
                    if ($pos + $bufferSize > $end) {
                        $bufferSize = $end - $pos + 1;
                    }
                    echo fread($file, $bufferSize);
                    flush();
                }

                fclose($file);
                exit;
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "File not found.";
                error_log("File not found: " . $file);
            }
        } else {
            // Display video player and episode navigation
            $directory = dirname($file);
            $files = glob($directory . '/*.{mp4,avi,mov,wmv,flv,ts,mkv}', GLOB_BRACE);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Video Stream</title>
                <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    .episode-list {
                        overflow-y: auto;
                        max-height: 650px;
                        min-width: 200px;
                        width: auto;
                        overflow: auto;
                    }
                    .episode-item {
                        display: flex;
                        align-items: center;
                        padding: 10px;
                        cursor: pointer;
                        max-width: 100%; 
                        box-sizing: border-box; 
                    }
                    .episode-thumbnail {
                        width: 120px;
                        height: 90px;
                        object-fit: cover;
                        margin-right: 10px;
                    }
                    .episode-details {
                        flex: 1;
                    }
                    .episode-title {
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        max-width: calc(100% - 20px); 
                    }
                    .card {
                        resize: both;
                        overflow: auto;
                    }
                </style>
            </head>
            <body>
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Pilih Episode Lain:</h3>
                            </div>
                            <div class="card-body episode-list">
                                <div class="list-group">
                                    <?php foreach ($files as $videoFile) : ?>
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?file=' . urlencode($videoFile); ?>" class="list-group-item list-group-item-action episode-item">
                                            <div class="episode-details">
                                                <div class="episode-title"><?php echo basename($videoFile); ?></div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h1 class="mb-0">Menonton: <?php echo basename($file); ?></h1>
                            </div>
                            <div class="card-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <video class="embed-responsive-item" controls>
                                        <source src="<?php echo $_SERVER['PHP_SELF'] . '?file=' . urlencode($file) . '&stream=true'; ?>" type="<?php echo $mimeType; ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <a href="index.php?dir=<?php echo urlencode($directory); ?>" class="btn btn-primary mt-3">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            </body>
            </html>
            <?php
        }
    } else {
        echo "File not found.";
        error_log("File not found: " . $file);
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo "No file specified.";
    error_log("No file specified.");
}

function limit_filename($filename, $limit) {
    if (strlen($filename) > $limit) {
        $filename = substr($filename, 0, $limit) . '...';
    }
    return $filename;
}
?>