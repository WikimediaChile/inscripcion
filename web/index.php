<?php

require_once '../vendor/autoload.php';

$fat = \Base::instance();

$fat->config('../config.ini');

$fat->route('GET /event/@permalink', '\route\event->render');
$fat->run();
