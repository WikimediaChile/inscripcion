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

    public function addParticipant(\Base $fat)
    {
        if ($fat->get('POST.token') !== $fat->get('SESSION.csrf')) {
            $fat->set('SESSION.error', ['code' => 0, 'message' => 'Error al enviar formulario, por favor reintente']);
            $fat->reroute('/event/'.$fat->get('PARAMS.permalink'));

            return $fat;
        }
        \model\participant::add($fat->get('POST.person'));
        \model\inscription::add($fat->get('PARAMS.permalink'), $fat->get('POST.person.username'));
        $fat->clear('SESSION.csrf');
        $fat->reroute('/event/'.$fat->get('PARAMS.permalink').'/subscribed');

        return $fat;
    }

    public function map(\Base $fat)
    {
    }

    public function end(\Base $fat)
    {
    }
}
