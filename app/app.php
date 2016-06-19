<?php

$app = new Silex\Application();

$app['debug'] = true;
$app['posts_root'] = getenv("ROOT_DIR")."/posts";
$app['webmentions_root'] = getenv("ROOT_DIR")."/webmentions";
$app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
$app['rpp'] = 9;
$app['token_endpoint'] = "https://tokens.indieauth.com/token";
$app['me_endpoint'] = "http://j4y.co/";

// PROVIDERS
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));
$app->register(new Silex\Provider\SessionServiceProvider());

// SERVICES
$app['monolog'] = $app->share(function () use ($app) {
    $log = new Monolog\Logger("aruna");
    $log->pushHandler(new Monolog\Handler\SyslogHandler('aruna'));
    return $log;
});
$app['db_cache'] = $app->share(function () use ($app) {
    $db = new \PDO("sqlite:".$app['db_file']);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $db;
});
$app['posts_repository_reader'] = $app->share(function () use ($app) {
    return new Aruna\PostRepositoryReaderFiles($app['db_cache']);
});
$app['mentions_repository_reader'] = $app->share(function () use ($app) {
    return new Aruna\MentionsRepositoryReader($app['db_cache']);
});
$app['posts_repository_writer'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local($app['posts_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    return new Aruna\PostRepositoryWriter($filesystem, $app['twig']);
});
$app['create_post.handler'] = $app->share(function () use ($app) {
    return new Aruna\CreatePostHandler(
        $app['posts_repository_writer']
    );
});

$app['access_token'] = $app->share(function () use ($app) {
    return new Aruna\AccessToken(
        $app['http_client'],
        $app['token_endpoint'],
        $app['me_endpoint']
    );
});

$app['action.create_post'] = $app->share(function () use ($app) {
    return new Aruna\CreatePostAction(
        $app["monolog"],
        $app["create_post.handler"],
        $app['access_token'],
        new Aruna\CreatePostResponder($app['url_generator'])
    );
});

$app['response'] = $app->share(function () {
    return new Symfony\Component\HttpFoundation\Response();
});

$app['action.show_micropub_form'] = $app->share(function () use ($app) {
    return new Aruna\ShowMicropubFormAction(
        new Aruna\ShowMicropubFormResponder($app['response'], $app['twig']),
        new Aruna\ShowMicropubFormHandler($app['session'])
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

$app['action.show_date_feed'] = $app->share(function () use ($app) {
    return new Aruna\ShowDateFeedAction(
        new Aruna\ShowLatestPostsResponder($app['response'], $app['twig']),
        new Aruna\CommandBus($app)
    );
});
$app['action.show_latest_posts'] = $app->share(function () use ($app) {
    return new Aruna\ShowLatestPostsAction(
        new Aruna\ShowLatestPostsResponder($app['response'], $app['twig']),
        new Aruna\CommandBus($app)
    );
});
$app['handler.showlatestposts'] = $app->share(function () use ($app) {
    return new Aruna\ShowLatestPostsHandler(
        $app['posts_repository_reader'],
        $app['url_generator']
    );
});
$app['handler.showdatefeed'] = $app->share(function () use ($app) {
    return new Aruna\ShowDateFeedHandler(
        $app['posts_repository_reader'],
        $app['url_generator']
    );
});

$app['action.show_post'] = $app->share(function () use ($app) {
    $handler = new Aruna\ShowPostHandler(
        $app['posts_repository_reader'],
        $app['url_generator']
    );
    return new Aruna\ShowPostAction(
        $handler,
        new Aruna\ShowPostResponder($app['response'], $app['twig'])
    );
});

$app['auth.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\AuthController(
        $app['http_client'],
        $app['monolog']
    );
});
$app['http_client'] = $app->share(function () {
    return new GuzzleHttp\Client();
});

$app['action.show.photos'] = $app->share(function () use ($app) {
    $handler = new Aruna\ShowPhotosHandler(
        $app['posts_repository_reader'],
        $app['url_generator']
    );
    return new Aruna\ShowPhotosAction(
        $handler,
        new Aruna\ShowPhotosResponder($app['response'], $app['twig'])
    );
});

require_once __DIR__ . "/routes.php";

return $app;
