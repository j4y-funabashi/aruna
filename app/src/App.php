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
        $app['me_endpoint'] = "http://j4y.co/";

        // PROVIDERS
        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../views',
        ));
        $app->register(new \Silex\Provider\SessionServiceProvider());
        $app->register(new Micropub\MicropubServiceProvider());
        $app->register(new Webmention\WebmentionServiceProvider());

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

        $app['action.show_date_feed'] = $app->share(function () use ($app) {
            return new ShowDateFeedAction(
                new ShowDateFeedResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                ),
                $app['handler.showdatefeed']
            );
        });

        $app['handler.showdatefeed'] = $app->share(function () use ($app) {
            return new ShowDateFeedHandler(
                $app['posts_repository_reader'],
                $app['url_generator']
            );
        });

        $app['action.show_post'] = $app->share(function () use ($app) {
            $handler = new ShowPostHandler(
                $app['posts_repository_reader'],
                $app['url_generator']
            );
            return new ShowPostAction(
                $handler,
                new ShowPostResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                )
            );
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

        $app['action.show.photos'] = $app->share(function () use ($app) {
            $handler = new ShowPhotosHandler(
                $app['posts_repository_reader'],
                $app['url_generator']
            );
            return new ShowPhotosAction(
                $handler,
                new ShowPhotosResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                )
            );
        });

        // ROUTES
        $app->get("/", 'action.show.photos:__invoke')
            ->bind('root');

        $app->get("/p/{post_id}", 'action.show_post:__invoke')
            ->bind('post');

        $app->get("/photos", "action.show.photos:__invoke")
            ->bind("photos");

        $app->get("/login", 'auth.controller:login')
            ->bind('login');

        $app->get("/auth", 'auth.controller:auth')
            ->bind('auth');

        $app->get("/{year}/{month}/{day}", 'action.show_date_feed:__invoke')
            ->value('month', '*')
            ->value('day', '*')
            ->bind('date_feed');

        return $app;
    }
}
