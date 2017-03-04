<?php

namespace model;

class wiki_user extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'wiki_user');
    }

    public static function add($data): bool
    {
        $user = new self();

        $user->copyfrom($data);

        $user->save();

        return true;
    }
}
