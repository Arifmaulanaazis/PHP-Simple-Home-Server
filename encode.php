<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = urldecode($_POST['filePath']);

    function exec_ffmpeg($command) {
        $output = array();
        exec($command . ' 2>&1', $output);
        return $output;
    }

    $ffmpegCommand = "./ffmpeg.exe -i $filePath -codec:v libx264 -preset slow -crf 22 -codec:a aac -strict experimental output.mp4";

    $output = exec_ffmpeg($ffmpegCommand);


    echo json_encode(['success' => true, 'output' => $output]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
