<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['mentions_root'] = getenv("ROOT_DIR")."/webmentions";
$app['processed_mentions_root'] = getenv("ROOT_DIR")."/processed_webmentions";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['thumbnails_root'] = getenv("ROOT_DIR")."/thumbnails";

// PROVIDERS

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});

$app['event_store'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local(getenv("ROOT_DIR"));
    $filesystem = new League\Flysystem\Filesystem($adapter);

    return new Aruna\EventStore($filesystem);
});

$app['process_cache_handler'] = $app->share(function () use ($app) {

    $db = new \PDO("sqlite:".$app['db_file']);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $linkPreview = new LinkPreview\LinkPreview();
    $linkPreview->addParser(new LinkPreview\Parser\GeneralParser());

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
        )
        ->pipe(
            new Aruna\Action\ConvertMarkdown(
                $app['monolog'],
                new \cebe\markdown\GithubMarkdown()
            )
        )
        ->pipe(
            new Aruna\Action\FetchLinkPreview(
                $app['monolog'],
                $linkPreview,
                $app['event_store']
            )
        )
        ->pipe(
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

    $adapter = new League\Flysystem\Adapter\Local($app['processed_mentions_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $mentionsWriter = new Aruna\MentionWriter($filesystem);

    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog'],
        $eventReader,
        $mentionsReader,
        $mentionsWriter,
        $pipeline,
        $app['processed_mentions_root']
    );
});

$app->command(new CLI\ProcessCacheCommand());

$app->run();
