<?php
include 'checkAuth.inc.php';

header('Content-Type: application/json; charset=utf-8');


$backups = '[]';
if (file_exists('../backups/backup.json')){
    $backups = json_encode(file_get_contents('../backups/backup.json'));
}
echo $backups;
