<?php

namespace Aruna\Micropub;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Aruna\RenderPost;
use Aruna\PostRepositoryReader;

class MicropubServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['posts_repository_reader'] = $app->share(function () use ($app) {
            return new PostRepositoryReader($app['db_cache']);
        });
        $app['response'] = $app->share(function () {
            return new \Symfony\Component\HttpFoundation\Response();
        });
        $app['action_create_post'] = $app->share(function () use ($app) {
            return new CreatePostAction(
                $app["create_post_handler"],
                new CreatePostResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                )
            );
        });
        $app['action_micropub_query'] = $app->share(function () use ($app) {
            return new QueryAction(
                new QueryHandler(),
                new QueryResponder(
                    $app['response'],
                    $app['twig'],
                    new RenderPost($app['twig'])
                )
            );
        });
        $app['access_token'] = $app->share(function () use ($app) {
            return new VerifyAccessToken(
                $app['http_client'],
                $app['token_endpoint'],
                $app['me_endpoint']
            );
        });
        $app['create_post_handler'] = $app->share(function () use ($app) {
            return new CreatePostHandler(
                $app["monolog"],
                $app['posts_repository_writer'],
                $app['access_token']
            );
        });
        $app['posts_repository_writer'] = $app->share(function () use ($app) {
            $adapter = new \League\Flysystem\Adapter\Local($app['posts_root']);
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            return new PostRepositoryWriter($filesystem, $app['db_cache']);
        });

        $app->post('/micropub', 'action_create_post:__invoke');
        $app->get("/micropub", "action_micropub_query:__invoke");
    }

    public function boot(Application $app)
    {
    }
}
