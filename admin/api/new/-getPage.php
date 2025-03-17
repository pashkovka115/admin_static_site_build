<?php
include 'File.php';

if (isset($_REQUEST['page'])) {
    $f = new File();
    echo json_encode($f->getFileAsStr($_REQUEST['page']), JSON_INVALID_UTF8_SUBSTITUTE);
}else{
    echo json_encode('<html lang="ru"><head><title>Ошибка</title></head><body>Не известный параметр.</body></html>');
}