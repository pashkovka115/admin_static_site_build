<?php
session_start();

define('DEBUG', false);


$query = basename($_SERVER['REQUEST_URI']);
$sp_query = explode('?', $query);

$controller = '';
$method = '';

if (isset($sp_query[0])) {
    $controller_method = explode('@', $sp_query[0]);
    if (isset($controller_method[0])) {
        $controller = $controller_method[0];
        if (isset($controller_method[1])) {
            $method = $controller_method[1];
        }
    }
}

$params = [];
if (isset($sp_query[1])) {
    $sp_params = explode('&', $sp_query[1]);
    foreach ($sp_params as $sp_param) {
        $p = explode('=', $sp_param);
        if (count($p) == 2) {
            $params[$p[0]] = $p[1];
        }
    }
}


spl_autoload_register(function ($class) {
    $class = str_replace('/', '\\', $class);
    if (file_exists(ucfirst($class) . '.php')) {
        include ucfirst($class) . '.php';
    }
});


if ($controller != '' && class_exists('Controllers\\' . $controller)) {
    $obj = new ('Controllers\\' . $controller)();
    if (method_exists($obj, $method)) {
        if (
            (isset($_SESSION['auth']) && $_SESSION['auth'] == true)
            or ($controller == 'auth' && $method == 'login')
        ){
            $obj->$method($params);
        }
    } else {
        if (DEBUG) {
            echo "В классе нет метода $method";
        }
    }
} else {
    if (DEBUG && !str_ends_with($controller, '.php')) {
        echo "Класс $controller отсутствует";
    }
}

