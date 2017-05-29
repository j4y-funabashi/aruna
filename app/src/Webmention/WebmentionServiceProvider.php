<?php

namespace Aruna\Webmention;

use Silex\Application;
use Silex\ServiceProviderInterface;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Aruna\RenderPost;

class WebmentionServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['response'] = $app->share(function () {
            return new \Symfony\Component\HttpFoundation\Response();
        });

        $app['webmentions_root'] = getenv("ROOT_DIR")."/webmentions";

        $app['action.receive_webmention'] = $app->share(function () use ($app) {
            $adapter = new Local($app['posts_root']);
            $filesystem = new Filesystem($adapter);
            $eventWriter = new EventWriter($filesystem);

            return new ReceiveWebmentionAction(
                new ReceiveWebmentionResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                ),
                new ReceiveWebmentionHandler(
                    new VerifyWebmentionRequest(),
                    $eventWriter,
                    $app["queue"]
                )
            );
        });

        $app['action.list_webmentions'] = $app->share(function () use ($app) {
            return new ListWebmentionsAction(
                new ListWebmentionsHandler(
                    new MentionsRepositoryReader($app["db_cache"])
                ),
                new ListWebmentionsResponder(
                    $app['response'],
                    $app['twig'],
                    $app["purifier"]
                )
            );
        });

        $app->post('/webmention', 'action.receive_webmention:__invoke');
        $app->get('/webmentions', 'action.list_webmentions:__invoke');
    }

    public function boot(Application $app)
    {
    }
}
