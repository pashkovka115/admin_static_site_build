<?php
include 'checkAuth.inc.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$file = $_POST['file'];
$page = $_POST['page'];

//echo json_encode($_POST);
//return;

if ($page && $file) {
    if (!copy('../backups/' . $file, '../../' . $page)){
        echo json_encode(['error'=>'Не удалось восстановить бэкап']);
    }
} else {
    header('HTTP/1.0 400 Bad Request');
}
