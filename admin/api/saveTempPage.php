<?php
include 'checkAuth.inc.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//echo 'Hello!';
//echo json_encode($_POST);
//return;

$new_file = '../../temp_temp_temp.html';

if (isset($_POST['html'])) {
    file_put_contents($new_file, $_POST['html']);
} else {
    header('HTTP/1.0 400 Bad Request');
}
