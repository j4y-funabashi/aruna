<?php

namespace Test;

use \Aruna\SendWebmention;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class SendWebmentionTest extends UnitTest
{
    public function setUp()
    {
        $this->event = array(
            "content" => "aruna webmentionz http://example.com"
        );
    }

    private function getClient($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_relative_link_headers()
    {
        $client = $this->getClient(
            [new Response(200, ['Link' => '</webmention?test=true>; rel=webmention'])]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_absolute_link_headers()
    {
        $client = $this->getClient(
            [new Response(200, ['Link' => '<http://example.com/webmention?test=true>; rel=webmention'])]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);

        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_relative_link_tags()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="webmention" href="/webmention?test=true">
            </head>
            <body></body>
            </html>
            ';
        $headers = [];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);

        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_absolute_link_tags()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="webmention" href="http://example.com/webmention?test=true">
            </head>
            <body></body>
            </html>
            ';
        $headers = [];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_relative_anchor_tags()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <a rel="webmention" href="/webmention?test=true">Webmention endpoint</a>
            </body>
            </html>
            ';
        $headers = [];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_absolute_anchor_tags()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <a rel="webmention" href="http://example.com/webmention?test=true">Webmention endpoint</a>
            </body>
            </html>
            ';
        $headers = [];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_strange_casing()
    {
        $client = $this->getClient(
            [new Response(200, ['LinK' => '<http://example.com/webmention?test=true>; rel=webmention'])]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);

        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_quoted_rel_value()
    {
        $client = $this->getClient(
            [new Response(200, ['LinK' => '<http://example.com/webmention?test=true>; rel="webmention"'])]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);

        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/9
     */
    public function it_discovers_endpoint_in_absolute_link_tags_with_multiple_rel_values()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="webmention somethingelse" href="http://example.com/webmention?test=true">
            </head>
            <body></body>
            </html>
            ';
        $headers = [];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/10
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_multiple_rel_values()
    {
        $client = $this->getClient(
            [new Response(200, ['Link' => '<http://example.com/webmention?test=true>; rel="webmention somethingelse"'])]
        );
        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);

        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/11
     */
    public function it_discovers_endpoint_in_absolute_link_headers_when_endpoints_are_also_in_link_tag_and_anchor()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="webmention" href="http://example.com/error">
            </head>
            <body>
            <a rel="webmention" href="http://example.com/error">Wrong endpoint</a>
            </body>
            </html>
            ';
        $headers = ['Link' => '<http://example.com/webmention?test=true>; rel="webmention"'];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/12
     */
    public function it_discovers_endpoint_when_page_contains_notwebmention_rel_values()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="not-webmention" href="http://example.com/error?linktag=true">
            </head>
            <body>
            <a rel="not-webmention" href="http://example.com/error?anchortag=true">Wrong endpoint</a>
            <a rel="webmention" href="/webmention?test=true">Correct endpoint</a>
            </body>
            </html>
            ';
        $headers = ['Link' => '<http://example.com/error?linkheader=true>; rel="not-webmention"'];
        $client = $this->getClient(
            [new Response(200, $headers, $body)]
        );

        $expected = "http://example.com/webmention?test=true";
        $SUT = new SendWebmention($client);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }
}
