<?php

namespace Aruna\Micropub;

use League\Pipeline\Pipeline;
use Aruna\DiscoverEndpoints;
use Aruna\FindUrls;

class ProcessingPipelineFactory
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function build($type)
    {
        switch ($type) {
            case 'CreatePost':
                return (new Pipeline())
                    ->pipe(
                        new ParseCategories()
                    )
                    ->pipe(
                        new CacheToSql(
                            $this->app['monolog'],
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
            case 'DeletePost':
                return (new Pipeline())
                    ->pipe(
                        new DeletePost(
                            $this->app['posts_repository_writer']
                        )
                    );
                break;
        }
    }
}
