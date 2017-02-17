<?php

require_once '../vendor/autoload.php';

$fat = \Base::instance();

$fat->config('../config.ini');

$fat->route('GET /event/@permalink', '\route\event->render');
$fat->route('POST /event/@permalink/suscribe', '\route\event->addParticipant');
$fat->route('GET /event/@permalink/map.jpg', '\route\event->map');
$fat->route('GET /event/@permalink/subscribed', '\route\event->end');
$fat->run();
