<?php
session_start();

if (isset($_SESSION['auth'])){
    unset($_SESSION['auth']);
}
$_SESSION = [];
