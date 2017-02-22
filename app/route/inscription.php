<?php

namespace route;

class inscription
{
    public function index(\Base $fat)
    {
        if ($fat->exists('GET.code')) {
            $fat->reroute('/remove/'.$fat->get('GET.code'));

            return $fat;
        }
        $fat->set('page', ['title' => 'Eliminar inscripción', 'content' => 'inscription.remove.form.html']);
        echo \Template::instance()->render('layout.basic.html');
    }

    public function remove(\Base $fat)
    {
        $rand = $fat->get('PARAMS.rand');
        try {
            $Inscription = \model\inscription::rand($rand);
        } catch (\Exception $e) {
            $fat->set('message', $e->getMessage());
            $fat->set('page', ['title' => 'Eliminar inscripción', 'content' => 'inscription.remove.error.html']);
            echo \Template::instance()->render('layout.basic.html');

            return $fat;
        }
        $Event = new \model\event();
        $Event->load(['evt_id = ?', $Inscription->insc_event]);
        $Inscription->erase();
        $fat->set('event', $Event);
        $fat->set('page.content', 'inscription.remove.success.html');
        echo \Template::instance()->render('layout.event.html');
    }
}
