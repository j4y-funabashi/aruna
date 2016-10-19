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

        $app['webmentions_root'] = getenv("ROOT_DIR")."/webmentions";

        $app['action.receive_webmention'] = $app->share(function () use ($app) {
            $adapter = new Local($app['webmentions_root']);
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
                    $eventWriter
                )
            );
        });

        $app->post('/webmention', 'action.receive_webmention:__invoke');
    }

    public function boot(Application $app)
    {
    }
}
