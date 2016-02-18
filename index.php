<?php

require_once __DIR__.'/vendor/autoload.php';

/*
 * ERROR HANDLING
 */
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set('display_startup_errors', 'On');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

$app = new Silex\Application();
$app['debug'] = true;

// SERVICES
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stdout',
    'monolog.name' => 'ftp_up'
));

$app['micropub.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\MicropubController(
        $app["monolog"]
    );
});

// ROUTES
$app->get("/micropub", function (Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return "Micropub form goes here";
});

$app->post('/micropub', 'micropub.controller:handle');

$app->run();
