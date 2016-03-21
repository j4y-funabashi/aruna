<?php

$app = new Silex\Application();

$app['debug'] = true;
$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['webmentions_root'] = getenv("ROOT_DIR")."/webmentions";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['rpp'] = 100;
$app['token_endpoint'] = "https://tokens.indieauth.com/token";
$app['me_endpoint'] = "http://j4y.co/";

// PROVIDERS
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stdout',
    'monolog.name' => 'aruna',
    'monolog.handler' => new Monolog\Handler\SyslogHandler('aruna')
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

// SERVICES
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
$app['posts_repository_writer'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local($app['posts_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    return new Aruna\PostRepositoryWriter($filesystem);
});
$app['create_post.handler'] = $app->share(function () use ($app) {
    return new Aruna\CreateEntryHandler(
        $app['posts_repository_writer']
    );
});
$app['micropub.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\MicropubController(
        $app["monolog"],
        $app["create_post.handler"],
        new Aruna\AccessToken(
            new GuzzleHttp\Client(),
            $app['token_endpoint'],
            $app['me_endpoint']
        )
    );
});
$app['webmention.controller'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local($app['webmentions_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    $eventWriter = new Aruna\EventWriter($filesystem);
    $eventReader = new Aruna\EventReader($filesystem);
    return new Aruna\Controller\WebmentionController(
        $app["monolog"],
        new Aruna\WebMention\WebMentionHandler(
            $eventWriter,
            $eventReader
        )
    );
});
$app['posts.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\PostController(
        $app['posts_repository_reader'],
        $app['mentions_repository_reader']
    );
});
$app['auth.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\AuthController(
        new GuzzleHttp\Client(),
        $app['monolog']
    );
});

require_once __DIR__ . "/routes.php";
