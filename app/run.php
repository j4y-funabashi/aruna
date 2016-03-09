<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['thumbnails_root'] = getenv("ROOT_DIR")."/thumbnails";

// PROVIDERS

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});

$app['process_cache_handler'] = $app->share(function () use ($app) {


    $pipeline = (new League\Pipeline\Pipeline())
        ->pipe(
            new Aruna\Action\ResizePhoto(
                $app['monolog'],
                new Aruna\Action\ImageResizer(
                    $app['monolog'],
                    $app['posts_root'],
                    $app['thumbnails_root']
                )
            )
        )->pipe(
            new Aruna\Action\ConvertMarkdown(
                $app['monolog'],
                new \cebe\markdown\GithubMarkdown()
            )
        );

    $adapter = new League\Flysystem\Adapter\Local($app['posts_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $eventReader = new Aruna\EventReader($filesystem);

    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog'],
        $eventReader,
        $pipeline
    );
});

$app->command(new CLI\ProcessCacheCommand());

$app->run();
