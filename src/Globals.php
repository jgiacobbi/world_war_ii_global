<?php

namespace Axis;

class Globals
{
    public static string $root = '';
    public static string $files = '';
    public static string $data = '';
    public static string $logs = '';

    public static function Init()
    {
        self::$root = dirname(__DIR__);
        if (self::$root == '/') {
            die('Why is the project root /, go fix it');
        }

        self::$files = self::$root.'/files';
        self::$data = self::$root.'/data';
        self::$logs = '/var/log/axis';
    }
}
