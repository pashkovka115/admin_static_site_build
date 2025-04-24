<?php

use Engine\File;
use Lib\DiDom\Document;
use Lib\DiDom\Element;

include "easycms.php";


//$base_href = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];

//$page = new \Controllers\Page();
//$page->getPage();

$f = new File();
$e = new \Engine\EditHTML();

echo $e->wrapTextNodes($f->getFileAsStr('index.html'));
