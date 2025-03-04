<?php
session_start();
if ($_SESSION['auth'] != true){
    header('HTTP/1.0 403 Forbidden');
    exit();
}
