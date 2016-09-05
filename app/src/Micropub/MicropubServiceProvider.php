<?php

namespace Aruna\Micropub;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MicropubServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['action.create_post'] = $app->share(function () use ($app) {
            return new CreatePostAction(
                $app["monolog"],
                $app["create_post.handler"],
                new CreatePostResponder(
                    $app['response'],
                    $app['twig'],
                    new \Aruna\RenderPost($app['twig'])
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
            $client = \Aws\S3\S3Client::factory([
                'credentials' => [
                    'key'    => getenv("S3_KEY"),
                    'secret' => getenv("S3_SECRET"),
                    ],
                    'region' => 'eu-west-1',
                    'version' => '2006-03-01',
                ]);
            $adapter = new \League\Flysystem\AwsS3v3\AwsS3Adapter($client, getenv("S3_BUCKET"), "posts");
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            return new PostRepositoryWriter($filesystem);
        });
    }

    public function boot(Application $app)
    {
    }
}
