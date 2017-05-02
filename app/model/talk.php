<?php

namespace model;

class talk extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'talk');
    }

    public static function permalink(string $permalink) : \DB\SQL\Mapper
    {
        $Self = new self();
        $Event = $Self->load(['talk_permalink = ?', $permalink]);
        if ($Event === false) {
            throw new \Exception('No se ha encontrado la charla!');
        }

        return $Event;
    }

}
