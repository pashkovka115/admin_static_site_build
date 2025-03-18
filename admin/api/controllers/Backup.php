<?php

namespace Controllers;

class Backup
{
    public function index()
    {
        header('Content-Type: application/json; charset=utf-8');


        $backups = '[]';
        if (file_exists('../backups/backup.json')){
            $backups = json_encode(file_get_contents('../backups/backup.json'));
        }
        echo $backups;
    }


    public function restore()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        $file = $_POST['file'];
        $page = $_POST['page'];

        if ($page && $file) {
            if (!copy('../backups/' . $file, '../../' . $page)){
                echo json_encode(['error'=>'Не удалось восстановить бэкап']);
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }
}