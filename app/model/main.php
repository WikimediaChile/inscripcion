<?php

namespace model;

class main
{
    private static $con;

    public static function instance() : \DB\SQL
    {
        if (isset(self::$con) === false) {
            $srv = sprintf('mysql:host=%s;dbname=%s', \F3::get('database.server'), \F3::get('database.database'));
            self::$con = new \DB\SQL($srv, \F3::get('database.user'), \F3::get('database.password'));
        }

        return self::$con;
    }
}
