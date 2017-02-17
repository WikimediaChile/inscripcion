<?php

namespace model;

class participant extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'participant');
    }

    public static function add(array $elements) : bool
    {
        $Participant = new self();

        # Check if participant is already on list
        $Participant->load(['part_username = ?', $elements['username']]);
        if ($Participant === false) {
            return true;
        }

        $toDatabase = [];
        foreach ($elements as $index => $element) {
            if ($index === 'morenews') {
                $element = (int) ((bool) $element);
            }
            $toDatabase['part_'.$index] = $element;
        }

        $Participant->copyFrom($toDatabase);
        $Participant->save();

        return true;
    }
}
