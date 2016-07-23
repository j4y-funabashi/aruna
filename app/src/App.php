<?php

namespace Aruna;

class App
{

    public static function build()
    {
        $app = new \Silex\Application();

        $app['debug'] = true;
        $app['posts_root'] = getenv("ROOT_DIR")."/posts";
        $app['webmentions_root'] = getenv("ROOT_DIR")."/webmentions";
        $app['db_file'] = getenv("ROOT_DIR")."/aruna_db.sq3";
        $app['rpp'] = 9;
        $app['token_endpoint'] = "https://tokens.indieauth.com/token";
        $app['me_endpoint'] = "http://j4y.co/";

        // PROVIDERS
        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../views',
        ));
        $app->register(new \Silex\Provider\SessionServiceProvider());

        // SERVICES
        $app['monolog'] = $app->share(function () use ($app) {
            $log = new \Monolog\Logger("aruna");
            $log->pushHandler(new \Monolog\Handler\SyslogHandler('aruna'));
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
        $app['posts_repository_writer'] = $app->share(function () use ($app) {
            $adapter = new \League\Flysystem\Adapter\Local($app['posts_root']);
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            return new PostRepositoryWriter($filesystem, $app['twig']);
        });
        $app['create_post.handler'] = $app->share(function () use ($app) {
            return new CreatePostHandler(
                $app['posts_repository_writer']
            );
        });

        $app['access_token'] = $app->share(function () use ($app) {
            return new AccessToken(
                $app['http_client'],
                $app['token_endpoint'],
                $app['me_endpoint']
            );
        });

        $app['action.create_post'] = $app->share(function () use ($app) {
            return new CreatePostAction(
                $app["monolog"],
                $app["create_post.handler"],
                $app['access_token'],
                new CreatePostResponder($app['url_generator'])
            );
        });

        $app['response'] = $app->share(function () {
            return new \Symfony\Component\HttpFoundation\Response();
        });

        $app['action.show_micropub_form'] = $app->share(function () use ($app) {
            return new ShowMicropubFormAction(
                new ShowMicropubFormResponder($app['response'], $app['twig']),
                new ShowMicropubFormHandler($app['session'])
            );
        });

        $app['webmention.controller'] = $app->share(function () use ($app) {
            $adapter = new \League\Flysystem\Adapter\Local($app['webmentions_root']);
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            $eventWriter = new EventWriter($filesystem);
            $eventReader = new EventReader($filesystem);
            return new Controller\WebmentionController(
                $app["monolog"],
                new WebMention\WebMentionHandler(
                    $eventWriter,
                    $eventReader
                )
            );
        });

        $app['action.show_date_feed'] = $app->share(function () use ($app) {
            return new ShowDateFeedAction(
                new ShowLatestPostsResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                ),
                new CommandBus($app)
            );
        });
        $app['action.show_latest_posts'] = $app->share(function () use ($app) {
            return new ShowLatestPostsAction(
                new ShowLatestPostsResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                ),
                new CommandBus($app)
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
            return new Controller\AuthController(
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

        $app->post('/micropub', 'action.create_post:__invoke');
        $app->get('/micropub', 'action.show_micropub_form:__invoke');

        $app->post('/webmention', 'webmention.controller:createMention');
        $app->get('/webmention/{mention_id}', 'webmention.controller:view')
            ->bind("webmention");

        $app->get("/{year}/{month}/{day}", 'action.show_date_feed:__invoke')
            ->value('month', '*')
            ->value('day', '*')
            ->bind('date_feed');

        return $app;
    }
}