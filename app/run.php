<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['mentions_root'] = getenv("ROOT_DIR")."/webmentions";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['thumbnails_root'] = getenv("ROOT_DIR")."/thumbnails";

// PROVIDERS

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});

$app['process_cache_handler'] = $app->share(function () use ($app) {

    $db = new \PDO("sqlite:".$app['db_file']);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

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
        )->pipe(
            new Aruna\Action\CacheToSql(
                $app['monolog'],
                $db
            )
        )
        ;

    $adapter = new League\Flysystem\Adapter\Local($app['posts_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $eventReader = new Aruna\EventReader($filesystem);

    $adapter = new League\Flysystem\Adapter\Local($app['mentions_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $mentionsReader = new Aruna\EventReader($filesystem);

    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog'],
        $eventReader,
        $mentionsReader,
        $pipeline
    );
});

$app->command(new CLI\ProcessCacheCommand());

$app->run();
