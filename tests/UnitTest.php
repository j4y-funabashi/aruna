<?php

namespace Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

/**
 * Class UnitTest
 * @author John Doe
 */
class UnitTest extends \PHPUnit_Framework_TestCase
{

    protected function loadJsonFixture($file)
    {
        return file_get_contents(__DIR__."/fixtures/".$file.".json");
    }

    protected function createHttpClient($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    protected function createEventStore()
    {
        $adapter = new \League\Flysystem\Memory\MemoryAdapter();
        $filesystem = new \League\Flysystem\Filesystem($adapter);
        return new \Aruna\EventStore($filesystem);
    }

    protected function createLogger()
    {
        $logger = new Logger('test');
        $logger->pushHandler(new TestHandler());
        return $logger;
    }
}
