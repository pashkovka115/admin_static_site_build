<?php
include 'checkAuth.inc.php';

if (isset($_REQUEST['name'])){
    $new_file = '../../' . $_REQUEST['name'] . '.html';

    if (file_exists($new_file)){
        header('HTTP/1.0 400 Bad Request');
    }else{
        fopen($new_file, 'w');
        echo json_encode([
            'response' => 'Файл создан.'
        ]);
    }
}else{
    echo json_encode($_REQUEST);
}
