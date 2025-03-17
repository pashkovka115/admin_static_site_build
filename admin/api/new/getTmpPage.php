<?php
include 'File.php';

$tmp_dir = '../../../';
$tmp_file = 'temp_temp_temp.html';
$full_path = $tmp_dir . $tmp_file;


if (file_exists($full_path)) {
    $f = new File();
    echo $f->getFileAsStr($full_path, true);
}else{
    echo '<html lang="ru"><head><title>Ошибка</title></head><body><p>Фйл не найден.</p><p>'.realpath($tmp_dir).'</p></body></html>';
}