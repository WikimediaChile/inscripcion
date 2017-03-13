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

    public function event_updateParticipants(\Base $fat)
    {
        if ($fat->exists('POST.part')) {
            foreach ($fat->get('POST.part') as $index => $value) {
                $Inscription = \model\inscription::rand($index);
                $Inscription->insc_attend = $value;
                $Inscription->save();
            }
            $message = ['code' => 1, 'message' => 'Participantes actualizados'];
        }
        if ($fat->get('AJAX') === false) {
            $fat->set('SESSION.error', $message);
            $fat->reroute('/coordination/'.$fat->get('PARAMS.permalink').'/list');
        } else {
            echo json_encode($message);
        }

        return $fat;
    }

    public function event_list(\Base $fat)
    {
        $fat->set('event', \model\event::permalink($fat->get('PARAMS.permalink')));
        $fat->set('participants', \model\participants::event($fat->get('PARAMS.permalink')));
        $fat->set('page.content', 'coordination.event.list.html');
        echo \Template::instance()->render('coordination.layout.html');
        $fat->clear('SESSION.error');
    }

    public function event_addParticipant(\Base $fat)
    {
        header('Content-Type: application/json');
        try {
            \model\participant::add($fat->get('POST.person'));
            \model\inscription::add($fat->get('PARAMS.permalink'), $fat->get('POST.person.username'));
        } catch (\Exception $e) {
            echo json_encode(['code' => 0, 'message' => $e->getMessage()]);

            return $fat;
        }
        echo json_encode(['code' => 1, 'message' => 'Agregado sin problemas']);

        return $fat;
    }

    public function beforeroute(\Base $fat)
    {
        if ($fat->exists('SESSION.token') === false) {
            $fat->reroute('/log.in');

            return $fat;
        }
    }
}
