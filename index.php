<?php
$baseDir = 'C:/Users/arifm/Videos/anime';
$currentDir = isset($_GET['dir']) ? urldecode($_GET['dir']) : $baseDir;



if (isset($_POST['deleteFile'])) {
    $filePathToDelete = urldecode($_POST['filePathToDelete']);
    if (file_exists($filePathToDelete)) {
        unlink($filePathToDelete);
    }
}

session_start();

if (isset($_POST['remoteUrl'])) {
    $remoteUrl = $_POST['remoteUrl'];
    $fileName = basename(parse_url($remoteUrl, PHP_URL_PATH));

    if (!empty($fileName)) {
        $filePath = 'path/to/your/folder' . $fileName;

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $_SESSION['download_progress'] = 0;

        $fp = fopen($filePath, 'w+');
        if ($fp === false) {
            die('Error: Unable to open file for writing.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded, $upload_size, $uploaded) {
            if ($download_size > 0) {
                $_SESSION['download_progress'] = round(($downloaded / $download_size) * 100);
            }
        });
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $_SESSION['download_progress'] = 100;
    } else {
        die('Error: Invalid file name.');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Viewer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        .file-container {
            display: flex;
            flex-wrap: wrap;
        }
        .file-item {
            width: 150px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
        }
        .back-item {
            cursor: pointer;
            width: 30px;
            display: flex;
            align-items: center;
        }
        .file-item i, .file-item video {
            font-size: 48px;
            width: 100%;
        }
        .context-menu {
            display: none;
            position: absolute;
            z-index: 1000;
            width: 200px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        .context-menu-item {
            padding: 10px;
            cursor: pointer;
        }
        .context-menu-item:hover {
            background-color: #f0f0f0;
        }
        .equal-height {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
</head>
<!-- Modal -->
<div class="modal fade" id="encodingModal" tabindex="-1" role="dialog" aria-labelledby="encodingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="encodingModalLabel">Encoding Progress</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Encoding file, please wait...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">File Viewer</h1>

    <div class="row mb-4">
        <div class="col-md-6 d-flex">
            <div class="card equal-height w-100">
                <div class="card-header">Buat Folder Baru</div>
                <div class="card-body flex-grow-1 d-flex flex-column">
                    <form method="post" class="flex-grow-1 d-flex flex-column">
                        <div class="form-group flex-grow-1 d-flex flex-column">
                            <label for="newFolderName">Nama Folder Baru:</label>
                            <input type="text" class="form-control flex-grow-1" id="newFolderName" name="newFolderName" required>
                        </div>
                        <button type="submit" name="createFolder" class="btn btn-primary mt-auto">Buat Folder</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card equal-height w-100">
                <div class="card-header">Unggah File</div>
                <div class="card-body flex-grow-1 d-flex flex-column">
                    <form id="uploadForm" method="post" enctype="multipart/form-data" class="flex-grow-1 d-flex flex-column">
                        <div class="form-group flex-grow-1 d-flex flex-column">
                            <label for="fileToUpload">Pilih File:</label>
                            <input type="file" class="form-control-file flex-grow-1" id="fileToUpload" name="fileToUpload" required>
                        </div>
                        <button type="submit" name="uploadFile" class="btn btn-primary mt-auto">Unggah</button>
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 d-flex">
            <div class="card equal-height w-100">
                <div class="card-header">Unduh File dari URL</div>
                <div class="card-body flex-grow-1 d-flex flex-column">
                    <form id="downloadForm" method="post" class="flex-grow-1 d-flex flex-column">
                        <div class="form-group flex-grow-1 d-flex flex-column">
                            <label for="remoteUrl">URL File:</label>
                            <input type="url" class="form-control flex-grow-1" id="remoteUrl" name="remoteUrl" required>
                        </div>
                        <button type="submit" name="remoteDownload" class="btn btn-primary mt-auto">Unduh</button>
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar" id="downloadProgressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; align-items: center;">
            <?php if ($currentDir != $baseDir): ?>
                <div class="back-item" data-path="<?php echo urlencode(dirname($currentDir)); ?>" data-type="folder" style="margin-right: 5px;">
                    <i class="fas fa-arrow-left fa-sm" style="margin-right: 5px;"></i>
                </div>
            <?php endif; ?>
            <span style="margin-right: 5px;">Lokasi:</span>
            <span><?php echo htmlspecialchars($currentDir); ?></span>
        </div>

        <div class="card-body file-container">
            <?php
            function truncateFileName($fileName, $maxLength = 15) {
                if (strlen($fileName) > $maxLength) {
                    return substr($fileName, 0, $maxLength) . '...';
                }
                return $fileName;
            }

            if (isset($_POST['createFolder'])) {
                $newFolderName = $_POST['newFolderName'];
                $newFolderPath = $currentDir . '/' . $newFolderName;
                if (!file_exists($newFolderPath)) {
                    mkdir($newFolderPath, 0777, true);
                }
            }

            if (isset($_FILES['fileToUpload'])) {
                $targetFile = $currentDir . '/' . basename($_FILES['fileToUpload']['name']);
                move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile);
            }

            $files = scandir($currentDir);
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
            $musicExtensions = ['mp3', 'wav', 'ogg', 'flac'];
            $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

            $folders = [];
            $otherFiles = [];

            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $filePath = $currentDir . '/' . $file;
                    if (is_dir($filePath)) {
                        $folders[] = $file;
                    } else {
                        $otherFiles[] = $file;
                    }
                }
            }

            $sortedFiles = array_merge($folders, $otherFiles);

            foreach ($sortedFiles as $file) {
                $filePath = $currentDir . '/' . $file;
                $fileSize = is_file($filePath) ? filesize($filePath) : 0;
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                $isDir = is_dir($filePath);
                $isVideo = in_array(strtolower($fileExtension), $videoExtensions);
                $isMusic = in_array(strtolower($fileExtension), $musicExtensions);
                $isDocument = in_array(strtolower($fileExtension), $documentExtensions);
                $displayName = truncateFileName($file);
                ?>
                <div class="file-item" 
                    data-path="<?php echo urlencode($filePath); ?>" 
                    data-type="<?php echo $isDir ? 'folder' : 'file'; ?>" 
                    data-size="<?php echo $fileSize; ?>" 
                    data-extension="<?php echo $fileExtension; ?>"
                    data-name="<?php echo $file; ?>">
                    <?php if ($isDir) { ?>
                        <i class="fas fa-folder"></i>
                    <?php } elseif ($isVideo) { ?>
                        <i class="fas fa-video"></i>
                    <?php } elseif ($isMusic) { ?>
                        <i class="fas fa-music"></i>
                    <?php } elseif ($isDocument) { ?>
                        <i class="fas fa-file-alt"></i>
                    <?php } else { ?>
                        <i class="fas fa-file"></i>
                    <?php } ?>
                    <div><?php echo $displayName; ?></div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- Context menu -->
<div id="contextMenu" class="context-menu">
    <div class="context-menu-item" id="downloadItem">Download</div>
    <div class="context-menu-item" id="watchItem">Lihat</div>
    <div class="context-menu-item" id="renameItem">Rename</div>
    <div class="context-menu-item" id="deleteItem">Hapus</div>
    <div class="context-menu-item" id="encodeMP4Item">Encode ke MP4</div>
    <div class="context-menu-item" id="infoItem">Info</div>
</div>

<form id="deleteForm" method="post" style="display:none;">
    <input type="hidden" name="deleteFile" value="1">
    <input type="hidden" id="filePathToDelete" name="filePathToDelete">
</form>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        let currentFile;

        $('.file-item').contextmenu(function(e) {
            e.preventDefault();
            currentFile = $(this);
            $('#contextMenu').css({
                top: e.pageY + 'px',
                left: e.pageX + 'px'
            }).show();
        });

        $(document).click(function() {
            $('#contextMenu').hide();
        });

        $('#downloadItem').click(function() {
            const path = currentFile.data('path');
            window.location.href = 'download.php?file=' + path;
        });

        $('#watchItem').click(function() {
            const path = currentFile.data('path');
            const extension = currentFile.data('extension').toLowerCase();
            const videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
            const musicExtensions = ['mp3', 'wav', 'ogg', 'flac'];
            if (videoExtensions.includes(extension)) {
                window.location.href = 'stream.php?file=' + path;
            } else if (musicExtensions.includes(extension)) {
                window.location.href = 'music.php?file=' + path;
            } else {
                alert('File tidak didukung.');
            }
        });

        $('#infoItem').click(function() {
            const size = currentFile.data('size');
            const type = currentFile.data('type');
            const fullName = currentFile.data('name');
            const info = `Name: ${fullName}\nType: ${type.charAt(0).toUpperCase() + type.slice(1)}\nSize: ${size} bytes`;
            alert(info);
        });

        $('#deleteItem').click(function() {
            const path = currentFile.data('path');
            if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
                $('#filePathToDelete').val(path);
                $('#deleteForm').submit();
            }
        });

        $('.file-item').click(function() {
            if ($(this).data('type') === 'folder') {
                window.location.href = '?dir=' + $(this).data('path');
            }
            if ($(this).data('type') === 'file') {
                const extension = $(this).data('extension').toLowerCase();
                const videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
                const musicExtensions = ['mp3', 'wav', 'ogg', 'flac'];
                if (videoExtensions.includes(extension)) {
                    window.location.href = 'stream.php?file=' + $(this).data('path');
                } else if (musicExtensions.includes(extension)) {
                    window.location.href = 'music.php?file=' + $(this).data('path');
                } else {
                    window.location.href = 'download.php?file=' + $(this).data('path');
                }
            }
        });

        $('.back-item').click(function() {
            window.location.href = '?dir=' + $(this).data('path');
        });

        $('#uploadForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.progress-bar').width(percentComplete + '%');
                            $('.progress-bar').html(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                type: 'POST',
                url: 'upload.php?dir=' + encodeURIComponent('<?php echo $currentDir; ?>'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Upload complete!');
                    window.location.reload();
                }
            });
        });

        $('#renameItem').click(function() {
            const path = currentFile.data('path');
            const newName = prompt('Masukkan nama baru:', currentFile.data('name'));
            if (newName !== null && newName.trim() !== '') {
                $.ajax({
                    type: 'POST',
                    url: 'rename.php',
                    data: {
                        filePath: path,
                        newName: newName
                    },
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error renaming file:', error);
                    }
                });
            }
        });

        $('#encodeMP4Item').click(function() {
            const path = currentFile.data('path');
            $.ajax({
                type: 'POST',
                url: 'encode.php',
                data: {
                    filePath: path
                },
                beforeSend: function() {
                    $('#encodingModal .modal-body').html('<p>Encoding file, please wait...</p>');
                    $('#encodingModal').modal('show');
                },
                success: function(response) {
                    console.log('Encoding process started.');
                    console.log(response.output); 

                    $('#encodingModal .modal-body').html('<pre>' + response.output.join('\n') + '</pre>');

                    alert('Encoding process completed.');
                },
                error: function(xhr, status, error) {
                    console.error('Error starting encoding:', error);
                    alert('Error starting encoding.');
                },
                complete: function() {
                    $('#encodingModal').modal('hide');
                }
            });
        });

    });
</script>
</body>
</html>
