<?php

class formaters extends \Prefab
{
    public static function format_time(string $time) : string
    {
        $DateTime = date_create_from_format('Y-m-d H:i:s', $time);

        return $DateTime->format('Y-m-d\TH:i');
    }

    public static function time_from_wiki(string $time) : string
    {
        $DateTime = date_create_from_format('Y-m-d\TH:i:s\Z', $time);

        return $DateTime->format('Y-m-d\TH:i:s');
    }

    public static function registry()
    {
        \Template::instance()->filter('format_time', '\formaters::instance()->format_time');
    }
}
