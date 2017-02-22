<?php

namespace route;

class user
{
    public function login(\Base $fat)
    {
        $fat->set('SESSION.csrf', substr(md5(rand()), 0, 10));
        echo \Template::instance()->render('user.form.html');
        $fat->clear('SESSION.error');
    }

    public function process(\Base $fat)
    {
        if ($fat->get('SESSION.csrf') !== $fat->get('POST.token')) {
            $fat->set('SESSION.error', 'Error en los token de sesión');
            $fat->reroute('/log.in');

            return $fat;
        }

        try {
            $User = \model\user::user($fat->get('POST.user'), $fat->get('POST.password'));
        } catch (\Exception $e) {
            $fat->set('SESSION.error', $e->getMessage());
            $fat->reroute('/log.in');

            return $fat;
        }
        $fat->set('SESSION.token', rand());
        $fat->reroute('/coordination');
    }
}
