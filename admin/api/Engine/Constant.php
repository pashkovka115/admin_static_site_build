<?php

namespace Engine;

class Constant{
    public static function BaseHref()
    {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
    }
    public static function BasePath()
    {
        return realpath(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
    }
    public static function TmpFile()
    {
        return 'temp_temp_temp.html';
    }
}
