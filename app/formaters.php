<?php

class formaters extends \Prefab
{
    public function format_time(string $time) : string
    {
        $DateTime = date_create_from_format('Y-m-d H:i:s', $time);

        return $DateTime->format('Y-m-d\TH:i');
    }
}
