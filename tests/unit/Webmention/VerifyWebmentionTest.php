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
    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_when_html_does_not_contain_target()
    {
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co/1",
            "mention_source_html" => ""
        );
        $SUT = new VerifyWebmention();
        $SUT->__invoke($mention);
    }

    /**
     * @test
     */
    public function it_returns_html_when_html_contains_target()
    {
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co/1",
            "mention_source_html" => "<a href='http://j4y.co/1'>hello</a>"
        );
        $SUT = new VerifyWebmention();
        $result = $SUT->__invoke($mention);
        $this->assertEquals($mention, $result);
    }
}
