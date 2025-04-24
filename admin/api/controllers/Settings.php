<?php

namespace Controllers;

use Engine\File;
use Engine\Header;

class Settings
{
    public function index()
    {
        Header::json();
        $f = new File();
        echo $f->getFileAsStr(File::path_document_root() . '/admin/api/settings.json', true);
    }
}
