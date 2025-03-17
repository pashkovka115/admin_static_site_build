<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'checkAuth.inc.php';


$htmlfiles = glob('../../*.html');

$files = [];
foreach ($htmlfiles as $htmlfile){
    $files[] = basename($htmlfile);
}
echo json_encode($files);
