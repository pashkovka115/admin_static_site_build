<?php

namespace Engine;

class Header
{
    public static function json()
    {
        header('Accept: application/json');
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function noCach()
    {
        header("Cache-Control: no-store");
        header("Pragma: no-cache");
    }
}