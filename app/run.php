<?php

require_once __DIR__ . "/../common.php";

$app = new Cilex\Application("aruna");

$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['thumbnails_root'] = getenv("ROOT_DIR")."/thumbnails";
$app['media_endpoint'] = "https://media.j4y.co/";

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
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
$app['mentions_repository_writer'] = $app->share(function () use ($app) {
    return new Aruna\Webmention\MentionsRepositoryWriter(
        $app['db_cache']
    );
});

$app['image_resizer'] = $app->share(function () use ($app) {
    return new Aruna\Publish\ImageResizer(
        $app['monolog'],
        getenv("ROOT_DIR"),
        $app['thumbnails_root']
    );
});
$app['event_store'] = $app->share(function () use ($app) {
    $adapter = new \League\Flysystem\Adapter\Local(getenv("ROOT_DIR"));
    $filesystem = new \League\Flysystem\Filesystem($adapter);
    return new Aruna\EventStore($filesystem);
});
$app['db_cache'] = $app->share(function () use ($app) {
    $db = new Aruna\Db("sqlite:".$app['db_file']);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $db;
});
$app['posts_repository_reader'] = $app->share(function () use ($app) {
    return new Aruna\PostRepositoryReader($app['db_cache']);
});
$app["event_log_repository"] = $app->share(function () use ($app) {
    return new Aruna\Publish\EventLogRepository($app["db_cache"]);
});

$app['posts_repository_writer'] = $app->share(function () use ($app) {
    $adapter = new \League\Flysystem\Adapter\Local($app['posts_root']);
    $filesystem = new \League\Flysystem\Filesystem($adapter);
    return new \Aruna\Micropub\PostRepositoryWriter($filesystem, $app['db_cache']);
});

$app["queue"] = $app->share(function () use ($app) {
    return new \Aruna\Queue(
        new \Pheanstalk\Pheanstalk(getenv("QUEUE_ADDRESS"))
    );
});

// processCacheProvider
$app['publish_posts_handler'] = $app->share(function () use ($app) {
    $pipelineFactory = new Aruna\Publish\ProcessingPipelineFactory($app);
    $eventProcessor = new Aruna\Publish\EventProcessor($pipelineFactory);
    return new Aruna\Publish\PublishPostsHandler(
        $app['monolog'],
        $app['event_log_repository'],
        $eventProcessor,
        $app["queue"]
    );
});

$app['remote_data_store'] = $app->share(function () use ($app) {
    $client = Aws\S3\S3Client::factory([
        'credentials' => [
            'key'    => getenv("S3_KEY"),
            'secret' => getenv("S3_SECRET"),
        ],
        'region' => 'eu-west-1',
        'version' => 'latest',
    ]);
    $adapter = new League\Flysystem\AwsS3v3\AwsS3Adapter(
        $client,
        getenv("S3_BUCKET")
    );
    $filesystem = new \League\Flysystem\Filesystem($adapter);
    return new Aruna\EventStore($filesystem);
});

$app['convert_data_handler'] = $app->share(function () use ($app) {
    return new Aruna\ConvertDataHandler(
        $app['monolog'],
        $app['event_store']
    );
});

$app['action.resize_photos'] = $app->share(function () use ($app) {
    return new Aruna\Publish\ResizePhotosAction(
        $app['monolog'],
        $app['event_store'],
        $app['image_resizer']
    );
});

$app["build_event_log_handler"] = $app->share(function () use ($app) {
    return new Aruna\Publish\BuildEventLogHandler(
        $app["monolog"],
        $app["event_store"],
        $app["event_log_repository"]
    );
});


$app['purifier'] = $app->share(function () use ($app) {
    $config = HTMLPurifier_Config::createDefault();
    return new HTMLPurifier($config);
});

$app->command(new CLI\BuildEventLogCommand());
$app->command(new CLI\ProcessCacheCommand());
$app->command(new CLI\ResizePhotoCommand());
$app->command(new CLI\ConvertJsonToMf2());

$app->run();
