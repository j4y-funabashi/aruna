<?php

namespace Aruna\Micropub;

use League\Pipeline\Pipeline;
use Aruna\Webmention\DiscoverEndpoints;
use Aruna\Webmention\FindUrls;

class ProcessingPipelineFactory
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function build($type)
    {
        switch ($type) {
            case 'PostCreated':
                return (new Pipeline())
                    ->pipe(
                        new ParseCategories()
                    )
                    ->pipe(
                        new CacheToSql(
                            $this->app['db_cache']
                        )
                    )
                    ->pipe(
                        new SendWebmention(
                            $this->app['http_client'],
                            new DiscoverEndpoints(),
                            new FindUrls(),
                            $this->app['monolog']
                        )
                    );
                break;
            case 'PostDeleted':
                return (new Pipeline())
                    ->pipe(
                        new DeletePost(
                            $this->app['posts_repository_writer']
                        )
                    )
                    ->pipe(
                        new CacheToSql(
                            $this->app['db_cache']
                        )
                    );
                break;
        }
    }
}
