<?php

namespace Controllers;

class Auth
{
    public function check()
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
            echo json_encode(['auth' => true]);
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['auth' => false]);
            exit();
        }
    }

    public function logout()
    {
        if (isset($_SESSION['auth'])){
            unset($_SESSION['auth']);
        }
        $_SESSION = [];
    }

    public function login()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
        if (isset($_POST['password'])){
            $password = $_POST['password'];

            if (file_exists('settings.json') && $password){
                header('Content-Type: application/json; charset=utf-8');
                if (!file_exists('settings.json')){
                    file_put_contents('settings.json', '{"password": "123456"}');
                }
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
        }

    }
}
