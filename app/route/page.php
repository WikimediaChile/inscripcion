<?php

namespace route;

class page
{
    public function index(\Base $fat)
    {
        $fat->mset(
            ['page.title' => 'Portal de eventos de Wikimedia Chile',
            'page.content' => 'home.html'
        ]);
        echo \Template::instance()->render('layout.basic.html');
    }
}
