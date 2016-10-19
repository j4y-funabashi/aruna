<?php

namespace Test;

use Aruna\Webmention\VerifyWebmention;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

/**
 * Class VerifyWebmentionTest
 * @author yourname
 */
class VerifyWebmentionTest extends UnitTest
{
    private function getGuzzle($response_body)
    {
        $mock = new MockHandler([
            new Response(200, [], $response_body)
        ]);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_when_html_does_not_contain_target()
    {
        $response_body = "";
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co/1"
        );
        $http = $this->getGuzzle($response_body);
        $log = $this->prophesize("Monolog\Logger");
        $SUT = new VerifyWebmention(
            $log,
            $http
        );

        $SUT->__invoke($mention);
    }

    /**
     * @test
     */
    public function it_returns_html_when_html_contains_target()
    {
        $response_body = "<a href='http://j4y.co/1'>hello</a>";
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co/1"
        );
        $http = $this->getGuzzle($response_body);
        $log = $this->prophesize("Monolog\Logger");
        $SUT = new VerifyWebmention(
            $log,
            $http
        );

        $result = $SUT->__invoke($mention);
        $expected = $response_body;
        $this->assertEquals($expected, $result);
    }
}
