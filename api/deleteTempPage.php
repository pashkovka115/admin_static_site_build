<?php
include 'checkAuth.inc.php';

$file = '../../temp_temp_tem.html';

if (file_exists($file)) {
    unlink($file);
} else {
    echo json_encode([
        'response' => 'Файл не существует.',
        'file' => basename($file)
    ]);
}