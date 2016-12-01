<?php

namespace Aruna\Reader;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Aruna\RenderPost;

class ReaderServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {
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

        $app['handler.showdatefeed'] = $app->share(function () use ($app) {
            return new ShowDateFeedHandler(
                $app['posts_repository_reader'],
                $app['url_generator']
            );
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

        $app->get("/", 'action.show.photos:__invoke')
            ->bind('root');

        $app->get("/p/{post_id}", 'action.show_post:__invoke')
            ->bind('post');

        $app->get("/photos", "action.show.photos:__invoke")
            ->bind("photos");

        $app->get("/{year}/{month}/{day}", 'action.show_date_feed:__invoke')
            ->value('month', '*')
            ->value('day', '*')
            ->bind('date_feed');
    }
}
