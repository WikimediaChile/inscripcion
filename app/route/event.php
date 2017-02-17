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

            return $fat;
        }

        $fat->set('SESSION.csrf', substr(md5(rand()), 0, 16));
        echo \Template::instance()->render('event_layout.html');
    }

    public function addParticipant(\Base $fat){

    }

    public function maps(\Base $fat){
        
    }

}
