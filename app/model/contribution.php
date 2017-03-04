<?php

namespace model;

class contribution extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'contribution');
    }

    public static function add($data): bool
    {
        $user = new self();
        $user->load(['con_revid = ?', $data['con_revid']]);
        if ($user->dry() === true) {
            $user->copyfrom($data);
            $user->save();
        }

        return true;
    }
}
