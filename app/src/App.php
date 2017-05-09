<?php

namespace Aruna;

class App
{

    public static function build()
    {
        $app = new \Silex\Application();

        $app['debug'] = (getenv("DEBUG"))
            ? getenv("DEBUG")
            : false;
        $app['posts_root'] = getenv("ROOT_DIR")."/posts";
        $app['rpp'] = 9;
        $app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
        $app['token_endpoint'] = "https://tokens.indieauth.com/token";
        $app['me_endpoint'] = "https://j4y.co/";
        $app['media_endpoint'] = "https://media.j4y.co/";

        $app['event_store'] = $app->share(function () use ($app) {
            $adapter = new \League\Flysystem\Adapter\Local(getenv("ROOT_DIR"));
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            return new \Aruna\EventStore($filesystem);
        });
        $app['mentions_repository_writer'] = $app->share(function () use ($app) {
            return new \Aruna\Webmention\MentionsRepositoryWriter(
                $app['db_cache']
            );
        });

        // PROVIDERS
        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../views',
        ));
        $app->register(new \Silex\Provider\SessionServiceProvider());
        $app->register(new Micropub\MicropubServiceProvider());
        $app->register(new Webmention\WebmentionServiceProvider());
        $app->register(new Reader\ReaderServiceProvider());

        // SERVICES
        $app['monolog'] = $app->share(function () use ($app) {
            $log = new \Monolog\Logger("aruna");
            $log->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout'));
            return $log;
        });
        $app['db_cache'] = $app->share(function () use ($app) {
            $db = new \Aruna\Db("sqlite:".$app['db_file']);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return $db;
        });

        $app['auth.controller'] = $app->share(function () use ($app) {
            return new AuthController(
                $app['http_client'],
                $app['monolog']
            );
        });
        $app['http_client'] = $app->share(function () {
            return new \GuzzleHttp\Client();
        });

        // ROUTES
        $app->get("/login", 'auth.controller:login')
            ->bind('login');

        $app->get("/auth", 'auth.controller:auth')
            ->bind('auth');

        return $app;
    }
}
