<?php
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);

    if (file_exists($file)) {
        if (is_dir($file)) {
            $folderName = basename($file);

            $zipFile = tempnam(sys_get_temp_dir(), 'folder_zip');

            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($file),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($files as $name => $fileInfo) {
                    if (!$fileInfo->isDir() && $fileInfo->isFile() && $fileInfo->isReadable()) {
                        $filePath = $fileInfo->getRealPath();
                        $relativePath = substr($filePath, strlen($file) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();

                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename=' . $folderName . '.zip');
                header('Content-Length: ' . filesize($zipFile));

                readfile($zipFile);

                unlink($zipFile);
                exit;
            } else {
                echo "Failed to create ZIP archive.";
            }
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            flush();
            readfile($file);
            exit;
        }
    } else {
        echo "File or folder not found.";
    }
} else {
    echo "No file specified.";
}
?>
