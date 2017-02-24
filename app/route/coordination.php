<?php

namespace route;

class coordination
{
    public function index(\Base $fat)
    {
        $Event = new \model\event();

        $fat->set('page.content', 'coordination.start.html');
        $fat->set('events.current', $Event->find(['now() between evt_startinscription and evt_endinscription']));
        echo \Template::instance()->render('coordination.layout.html');
    }

    public function beforeroute(\Base $fat)
    {
        if ($fat->exists('SESSION.token') === false) {
            $fat->reroute('/log.in');

            return $fat;
        }
    }
}
