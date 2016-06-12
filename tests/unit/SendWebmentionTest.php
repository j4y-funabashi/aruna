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

    private function getSUT($headers, $body, $response_code = 200)
    {
        $client = $this->getClient(
            [new Response($response_code, $headers, $body)]
        );
        return new SendWebmention($client);
    }

    /**
     * @test
     */
    public function it_returns_an_empty_string_if_no_endpoint_is_discovered()
    {
        $headers = [];;
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body></body>
            </html>
            ';

        $SUT = $this->getSUT($headers, $body);
        $result = $SUT->__invoke($this->event);
        $expected = "";
        $this->assertEquals($expected, $result);
    }


    /**
     * @test
     * https://webmention.rocks/test/1
     */
    public function it_discovers_endpoint_in_relative_link_headers()
    {
        $headers = array('Link' => '</webmention?test=true>; rel=webmention');
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/2
     */
    public function it_discovers_endpoint_in_absolute_link_headers()
    {
        $headers = array('Link' => '<http://example.com/webmention?test=true>; rel=webmention');
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/3
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/4
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/5
     */
    public function it_discovers_endpoint_in_relative_anchor_tags()
    {
        $headers = [];
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/6
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/7
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_strange_casing()
    {
        $headers = array('LinK' => '<http://example.com/webmention?test=true>; rel=webmention');
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/8
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_quoted_rel_value()
    {
        $headers = ['LinK' => '<http://example.com/webmention?test=true>; rel="webmention"'];
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/10
     */
    public function it_discovers_endpoint_in_absolute_link_headers_with_multiple_rel_values()
    {
        $headers = ['Link' => '<http://example.com/webmention?test=true>; rel="webmention somethingelse"'];
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
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
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/13
     */
    public function it_ignores_endpoint_in_anchor_tags_within_html_comments()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <div class="e-content">
            <!-- <a href="/webmention/error" rel="webmention"></a> -->
            <a href="/webmention?test=true" rel="webmention">correct endpoint</a>
            </div>
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/14
     */
    public function it_ignores_endpoint_in_anchor_tags_within_escaped_html()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <div class="e-content">
            <code>&lt;a href="/webmention/error" rel="webmention"&gt;&lt;/a&gt;</code>
            <a href="/webmention?test=true" rel="webmention">correct endpoint</a>
            </div>
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/15
     */
    public function it_discovers_endpoint_in_link_tag_with_empty_href()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="webmention" href="">
            </head>
            <body></body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/16
     */
    public function it_discovers_endpoint_in_anchor_when_anchor_is_first()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <a href="/webmention?test=true" rel="webmention">&lt;a&gt; tag</a>
            <link rel="webmention" href="/webmention/error">
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/17
     */
    public function it_discovers_endpoint_in_link_when_link_is_first()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <link rel="webmention" href="/webmention?test=true">
            <a href="/webmention/error" rel="webmention">&lt;a&gt; tag</a>
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/18
     */
    public function it_discovers_endpoint__when_multiple_link_headers_are_returned()
    {
        $headers = [
            'LinK' => '<http://example.com/error>; rel=other',
            'Link' => '<http://example.com/webmention?test=true>; rel=webmention'
        ];
        $body = "";
        $expected = "http://example.com/webmention?test=true";
        $SUT = $this->getSUT($headers, $body);
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/19
     */
    public function it_discovers_endpoint_from_a_link_header_with_multiple_values()
    {
        $headers = [
            'Link' => '<http://example.com/error>; rel=other, <http://example.com/webmention?test=true>; rel=webmention'
        ];
        $body = "";
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/20
     */
    public function it_discovers_endpoint_in_anchor_when_anchor_is_followed_by_link_tag_with_no_href()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <a href="/webmention?test=true" rel="webmention">&lt;a&gt; tag</a>
            <link rel="webmention">
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * https://webmention.rocks/test/21
     */
    public function it_discovers_endpoint_and_preserves_query_string()
    {
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            </head>
            <body>
            <link href="/webmention?test=true" rel="webmention">
            </body>
            </html>
            ';
        $headers = [];
        $SUT = $this->getSUT($headers, $body);
        $expected = "http://example.com/webmention?test=true";
        $result = $SUT->__invoke($this->event);
        $this->assertEquals($expected, $result);
    }
}
