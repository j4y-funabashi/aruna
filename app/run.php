<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

// PROVIDERS

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});

$app['process_cache_handler'] = $app->share(function () use ($app) {
    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog']
    );
});

$app->command(new CLI\ProcessCacheCommand());

$app->run();
