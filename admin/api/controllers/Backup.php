<?php

namespace Controllers;

use Engine\File;
use Engine\Header;

class Backup
{
    public function index()
    {
        Header::json();

        $file = new File();
        $backup_json = $file->getFileAsStr('../backups/backup.json', true);
        if ($backup_json) {
            echo json_encode($backup_json);
        } else {
            echo json_encode('""');
        }
    }


    public function restore()
    {
        if (isset($_POST['file']) && isset($_POST['page'])) {
            $file = $_POST['file'];
            $page = $_POST['page'];

            if ($page && $file) {
                if (!copy('../backups/' . $file, '../../' . $page)) {
                    echo json_encode(['error' => 'Не удалось восстановить бэкап']);
                }
            } else {
                header('HTTP/1.0 400 Bad Request');
            }
        }
    }


    public function delete()
    {
        if (isset($_POST['item'])) {
            $item = $_POST['item'];

            if ($item) {
                if (!unlink('../backups/' . $item['file'])) {
                    echo json_encode(['error' => 'Не удалось удалить файл из бэкапа']);
                    return;
                }

                $backups = [];
                if (file_exists('../backups/backup.json')) {
                    $backups = json_decode(file_get_contents('../backups/backup.json'), true);
                }
                foreach ($backups as $key => $backup){
                    if ($backup['time'] == $item['time']){
                        unset($backups[$key]);
                    }
                }
                file_put_contents('../backups/backup.json', json_encode($backups, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));

                echo json_encode(['success' => 'Файл из бэкапа удалён', 'item' => $item]);
            } else {
                header('HTTP/1.0 400 Bad Request');
            }
        }else{
            echo json_encode(['error' => 'Не коректный параметр']);
        }
    }
}
