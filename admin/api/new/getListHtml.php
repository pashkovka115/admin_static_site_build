<?php
include 'File.php';

$f = new File();

echo json_encode($f->listHTML());
