<?php

namespace Controllers;

use Engine\File;

class Page
{
    public function index()
    {
        $f = new File();

        echo json_encode($f->listHTML());
    }

    public function savePage()
    {
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

    }

    public function getPage()
    {
        if (isset($_REQUEST['page'])) {
            $f = new File();
            echo json_encode($f->getFileAsStr($_REQUEST['page']), JSON_INVALID_UTF8_SUBSTITUTE);
        }else{
            echo json_encode('<html lang="ru"><head><title>Ошибка</title></head><body>Не известный параметр.</body></html>');
        }
    }

    public function saveTempPage()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        $new_file = '../../temp_temp_temp.html';

        if (isset($_POST['html'])) {
            file_put_contents($new_file, $_POST['html']);
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }

    public function getTempPage()
    {
        $f = new File();
        $tmp_file = $f->getFileAsStr('temp_temp_temp.html');

        if ($tmp_file) {
            echo $tmp_file;
        }else{
            echo '<html lang="ru"><head><title>Ошибка</title></head><body><p>Фйл не найден.</p><p></p></body></html>';
        }
    }

    public function deleteTempPage()
    {
        $file = '../../temp_temp_temp.html';

        if (file_exists($file)) {
            unlink($file);
        } else {
            echo json_encode([
                'response' => 'Файл не существует.',
                'file' => basename($file)
            ]);
        }
    }
}