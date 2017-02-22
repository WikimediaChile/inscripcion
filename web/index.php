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

$fat->run();
