<?php
include 'checkAuth.inc.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$file = $_POST['pagename'];
$new_html = $_POST['html'];

if (!is_dir('../backups/')){
    mkdir('../backups/');
}

$backups = [];
if (file_exists('../backups/backup.json')){
    $backups = json_decode(file_get_contents('../backups/backup.json'));
}

if ($new_html && $file) {
    $backupFN = uniqid() . '.html';
    copy('../../' . $file, '../backups/' . $backupFN);
    $backups[] = ['page' => basename($file), 'file' => $backupFN, 'time'=> date('H:i:s d-m-Y')];
    file_put_contents('../backups/backup.json', json_encode($backups));

    file_put_contents('../../' . $file, $new_html);
} else {
    header('HTTP/1.0 400 Bad Request');
}
