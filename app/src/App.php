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
            $db = new \PDO("sqlite:".$app['db_file']);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return $db;
        });
        $app['posts_repository_reader'] = $app->share(function () use ($app) {
            return new PostRepositoryReader($app['db_cache']);
        });

        $app['response'] = $app->share(function () {
            return new \Symfony\Component\HttpFoundation\Response();
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
