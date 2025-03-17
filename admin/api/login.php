<?php
session_start();

$_POST = json_decode(file_get_contents('php://input'), true);
$password = $_POST['password'];

if (file_exists('settings.json') && $password){
    $settings = json_decode(file_get_contents('settings.json'), true);
    if ($password == $settings['password']){
        $_SESSION['auth'] = true;
        echo json_encode(['auth' => true]);
    }else {
        echo json_encode(['auth' => false]);
    }
}else{
    header('HTTP/1.0 400 Bad Request');
}

