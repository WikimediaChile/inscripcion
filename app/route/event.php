<?php

namespace route;

class event
{
    public function render(\Base $fat)
    {
        try {
            $fat->set('event', \model\event::permalink($fat->get('PARAMS.permalink')));
        } catch (\Exception $e) {
            $fat->error(404, $e->getMessage());
        }
    }
}
