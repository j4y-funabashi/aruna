<?php

namespace Aruna\Micropub;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Aruna\RenderPost;

class MicropubServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['action.create_post'] = $app->share(function () use ($app) {
            return new CreatePostAction(
                $app["create_post.handler"],
                new CreatePostResponder(
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
        $app['create_post.handler'] = $app->share(function () use ($app) {
            return new CreatePostHandler(
                $app['posts_repository_writer'],
                $app['access_token']
            );
        });
        $app['posts_repository_writer'] = $app->share(function () use ($app) {
            $adapter = new \League\Flysystem\Adapter\Local($app['posts_root']);
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            return new PostRepositoryWriter($filesystem, $app['db_cache']);
        });

        $app->post('/micropub', 'action.create_post:__invoke');
    }

    public function boot(Application $app)
    {
    }
}
