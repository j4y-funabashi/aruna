<?php

require_once __DIR__.'/vendor/autoload.php';

Symfony\Component\Debug\ErrorHandler::register();

$dotenv = new Dotenv\Dotenv("..");
$dotenv->load();

$app = new Silex\Application();
$app['debug'] = true;
$app['filestore_root'] = "/tmp/aruna";

// PROVIDERS
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stdout',
    'monolog.name' => 'aruna'
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// SERVICES
$app['posts_repository'] = $app->share(function () use ($app) {
    $adapter = new League\Flysystem\Adapter\Local($app['filestore_root']);
    $filesystem = new League\Flysystem\Filesystem($adapter);
    return new Aruna\EntryRepository($filesystem);
});
$app['create_post.handler'] = $app->share(function () use ($app) {
    return new Aruna\CreateEntryHandler(
        $app['posts_repository'],
        new Aruna\ImageResizer($app['filestore_root'])
    );
});
$app['micropub.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\MicropubController(
        $app["monolog"],
        $app["create_post.handler"]
    );
});
$app['posts.controller'] = $app->share(function () use ($app) {
    return new Aruna\Controller\PostController($app['posts_repository']);
});

// ROUTES
$app->get("/", 'posts.controller:feed')
    ->bind('post_feed');
$app->get("/p/{post_id}", 'posts.controller:getById')
    ->bind('post');
$app->post('/micropub', 'micropub.controller:createPost');

$app->run();
