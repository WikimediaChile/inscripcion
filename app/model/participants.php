<?php

namespace model;

class participants extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'participants');
    }

    public static function event(string $permalink) : array
    {
        $List = new self();

        return $List->find(['evt_permalink = ?', $permalink], ['order' => 'part_name']);
    }
}
