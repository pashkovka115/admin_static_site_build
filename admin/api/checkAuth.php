<?php
session_start();

if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
    echo json_encode(['auth' => true]);
}else{
    echo json_encode(['auth' => false]);
}
