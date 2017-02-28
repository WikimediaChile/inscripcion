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

$fat->route('GET /log.in', '\route\user->login');
$fat->route('POST /log.in', '\route\user->process');
$fat->route('GET /coordination', '\route\coordination->index');
$fat->route('GET /coordination/@permalink', '\route\coordination->event_details');
$fat->route('GET /coordination/@permalink/metrics', '\route\coordination->event_metrics');
$fat->route('POST /coordination/@permalink/update', '\route\coordination->event_update');
$fat->route('GET /coordination/@permalink/list', '\route\coordination->event_list');

\Template::instance()->filter('format_time','\formaters::instance()->format_time');

$fat->run();
