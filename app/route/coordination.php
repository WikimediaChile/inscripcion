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

    public function event_details(\Base $fat)
    {
        $Event = \model\event::permalink($fat->get('PARAMS.permalink'));

        $fat->set('event', $Event);
        $fat->set('page.content', 'coordination.event.details.html');
        echo \Template::instance()->render('coordination.layout.html');
        $fat->clear('SESSION.error');
    }

    public function event_update(\Base $fat)
    {
        $Event = \model\event::permalink($fat->get('PARAMS.permalink'));
        $event = [];
        foreach ($fat->get('POST.event') as $index => $value) {
            $event['evt_'.$index] = $value;
        }
        $Event->copyfrom($event);
        $Event->save();

        $fat->set('SESSION.error', ['code' => 1, 'message' => 'Evento actualizado']);
        $fat->reroute('/coordination/'.$fat->get('PARAMS.permalink'));
    }

    public function event_list(\Base $fat)
    {
        $fat->set('event', \model\event::permalink($fat->get('PARAMS.permalink')));
        $fat->set('participants', \model\participants::event($fat->get('PARAMS.permalink')));
        $fat->set('page.content', 'coordination.event.list.html');
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
