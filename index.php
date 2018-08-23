<?php

define ( 'ROOT_DIR', __DIR__ );

require_once('vendor/autoload.php');
require_once('app/core/loader.php');

$controller = new Loader();
$controller = $controller->createController();
$controller->action();