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
    $log->pushHandler(new Monolog\Handler\StreamHandler('php://stdout'));
    return $log;
});
$app['http_client'] = $app->share(function () {
    return new GuzzleHttp\Client(
        array(
            'timeout'  => 20.0,
        )
    );
});

$app['image_resizer'] = $app->share(function () use ($app) {
    return new Aruna\Action\ImageResizer(
        $app['monolog'],
        getenv("ROOT_DIR"),
        $app['thumbnails_root']
    );
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

$app['process_cache_handler'] = $app->share(function () use ($app) {

    $processPostsPipeline = (new League\Pipeline\Pipeline())
        ->pipe(
            new Aruna\Pipeline\ParseCategories()
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

    return new Aruna\Handler\ProcessCacheHandler(
        $app['monolog'],
        $app['event_store'],
        $processPostsPipeline,
        $app['posts_repository_reader']
    );
});

$app['action.resize_photos'] = $app->share(function () use ($app) {
    return new Aruna\ResizePhotosAction(
        $app['monolog'],
        $app['event_store'],
        $app['image_resizer']
    );
});

$app->command(new CLI\ProcessCacheCommand());
$app->command(new CLI\ResizePhotoCommand());

$app->run();
