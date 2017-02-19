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
        try {
            \model\participant::add($fat->get('POST.person'));
            \model\inscription::add($fat->get('PARAMS.permalink'), $fat->get('POST.person.username'));
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 0:
                    $fat->set('SESSION.error', ['code' => $e->getCode(), 'message' => $e->getMessage()]);
                    break;
                case 1:
                    $fat->reroute('/event/'.$fat->get('PARAMS.permalink').'/subscribed');
                    break;
            }
        }

        $fat->clear('SESSION.csrf');
        $fat->reroute('/event/'.$fat->get('PARAMS.permalink').'/subscribed');

        return $fat;
    }

    public function map(\Base $fat)
    {
        $file = $fat->get('TEMP').'map-'.$fat->get('PARAMS.permalink');

        if (file_exists($file) === false) {
            $Evt = \model\event::permalink($fat->get('PARAMS.permalink'));
            $map = new \Web\Google\StaticMap();

            $map->center($Evt->evt_place);
            $map->format('jpg');
            $map->size('600x300');
            $map->markers('color:blue|label:W|'.$Evt->evt_place);
            $map->zoom('14');
            $map->sensor('false');
            file_put_contents($file, $map->dump());
        }
        header('Content-type: image/jpg');
        echo file_get_contents($file);
    }

    public function end(\Base $fat)
    {
    }
}
