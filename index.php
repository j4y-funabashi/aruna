<?php

/*
 * ERROR HANDLING
 */
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('display_startup_errors', 'On');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->run();
