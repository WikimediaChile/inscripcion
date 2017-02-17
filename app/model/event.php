<?php

namespace model;

class event extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'event');
    }

    public static function permalink(string $permalink) : \DB\SQL\Mapper
    {
        $Self = new self();
        $Event = $Self->load(['evt_permalink = ?', $permalink]);
        if($Event === false){
            throw new \Exception('No se ha encontrado el evento!');
        }
        return $Event;
    }
}
