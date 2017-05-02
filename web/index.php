<?php

require_once '../vendor/autoload.php';

$fat = \Base::instance();

$fat->config('../config.ini');

$fat->route('GET /@permalink', '\route\event->render');
$fat->route('POST /@permalink/suscribe', '\route\event->addParticipant');
$fat->route('GET /@permalink/map.jpg', '\route\event->map');
$fat->route('GET /@permalink/subscribed', '\route\event->end');
$fat->route('GET /remove', '\route\inscription->index');
$fat->route('GET /remove/@rand', '\route\inscription->remove');
$fat->route('GET /', '\route\page->index');

$fat->route('GET /log.in', '\route\user->login');
$fat->route('POST /log.in', '\route\user->process');
$fat->route('GET /coordination', '\route\coordination->index');
$fat->route('GET /coordination/@permalink', '\route\coordination->event_details');
$fat->route('GET /coordination/@permalink/metrics', '\route\coordination->event_metrics');
$fat->route('GET /coordination/@permalink/list', '\route\coordination->event_list');
$fat->route('POST /coordination/@permalink/updateParticipants', '\route\coordination->event_updateParticipants');
$fat->route('POST /coordination/@permalink/update', '\route\coordination->event_update');
$fat->route('POST /coordination/updateParticipants [ajax]', '\route\coordination->event_updateParticipants');
$fat->route('POST /coordination/@permalink/addParticipant [ajax]', '\route\coordination->event_addParticipant');

\formaters::registry();

$cron = \Cron::instance();
$cron->web = true;
$cron->silent = false;
$cron->set('contributions', '\contributions->job', '5/* * * * *');
$cron->set('all', '\contributions->job2', '5/* * * * *');

$fat->run();
