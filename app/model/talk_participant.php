<?php

namespace model;

class talk_participant extends \DB\SQL\Mapper
{
    public function __construct()
    {
        parent::__construct(main::instance(), 'talk_participant');
    }

    public static function add(array $elements, string $permalink) : bool
    {
        $Participant = new self();

        $toDatabase = [];
        foreach ($elements as $index => $element) {
            if ($index === 'morenews') {
                $element = (int) ((bool) $element);
            }
            $toDatabase['tp_'.$index] = $element;
        }

        $Talk = talk::permalink($permalink);
        $toDatabase['tp_talk'] = $Talk->talk_id;

        $Participant->copyFrom($toDatabase);
        $Participant->save();

        return true;
    }
}
