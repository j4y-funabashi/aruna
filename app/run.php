<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['thumbnails_root'] = getenv("ROOT_DIR")."/thumbnails";

// PROVIDERS

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});
$app['http_client'] = $app->share(function () {
    return new GuzzleHttp\Client();
});

$app['event_store'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local(getenv("ROOT_DIR"));
    $filesystem = new League\Flysystem\Filesystem($adapter);
    return new Aruna\EventStore($filesystem);
});
$app['db_cache'] = $app->share(function () use ($app) {
    $db = new \PDO("sqlite:".$app['db_file']);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $db;
});
$app['posts_repository_reader'] = $app->share(function () use ($app) {
    return new Aruna\PostRepositoryReader($app['db_cache']);
});
$app['mentions_repository_reader'] = $app->share(function () use ($app) {
    return new Aruna\MentionsRepositoryReader($app['db_cache']);
});

$app['process_cache_handler'] = $app->share(function () use ($app) {

    $linkPreview = new LinkPreview\LinkPreview();
    $linkPreview->addParser(new LinkPreview\Parser\GeneralParser());

    $processPostsPipeline = (new League\Pipeline\Pipeline())
        ->pipe(
            new Aruna\Pipeline\PostTypeDiscovery()
        )
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
            new Aruna\Pipeline\ConvertMarkdown(
                $app['monolog'],
                new \cebe\markdown\GithubMarkdown()
            )
        )
        ->pipe(
            new Aruna\Pipeline\ParseCategories()
        )
        ->pipe(
            new Aruna\Pipeline\FetchLinkPreview(
                $app['monolog'],
                $linkPreview,
                $app['event_store']
            )
        )
        ->pipe(
            new Aruna\Action\CacheToSql(
                $app['monolog'],
                $app['db_cache']
            )
        )
        ->pipe(
            new Aruna\SendWebmention(
                $app['http_client'],
                new Aruna\DiscoverEndpoints(),
                new Aruna\FindUrls(),
                $app['monolog']
            )
        )
        ;

    $processMentionsPipeline = (new League\Pipeline\Pipeline())
        ->pipe(
            new Aruna\Action\ParseWebMention(
                $app['monolog'],
                $app['event_store']
            )
        )
        ->pipe(
            new Aruna\Action\CacheMentionToSql(
                $app['db_cache']
            )
        )
        ;

    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog'],
        $app['event_store'],
        $processPostsPipeline,
        $processMentionsPipeline,
        $app['posts_repository_reader'],
        $app['mentions_repository_reader']
    );
});

$app->command(new CLI\ProcessCacheCommand());

$app->run();
