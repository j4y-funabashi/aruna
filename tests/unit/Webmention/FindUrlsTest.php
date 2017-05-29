<?php

namespace Test;

use Aruna\Webmention\FindUrls;

class FindUrlsTest extends UnitTest
{

    /**
     * @test
     */
    public function it_finds_http_url()
    {
        $SUT = new FindUrls();
        $in = "http://example.com";
        $expected = array("http://example.com");
        $result = $SUT->__invoke($in);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_finds_multiple_http_urls_when_input_has_whitespace_chars()
    {
        $SUT = new FindUrls();
        $in = 'https://webmention.rocks/test/1\r\nhttps://webmention.rocks/test/2';
        $expected = array(
            "https://webmention.rocks/test/1",
            "https://webmention.rocks/test/2"
        );
        $result = $SUT->__invoke($in);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_finds_multiple_http_url()
    {
        $SUT = new FindUrls();
        $in = "http://example.com http://example.com/1";
        $expected = array(
            "http://example.com",
            "http://example.com/1"
        );
        $result = $SUT->__invoke($in);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_dedupes_return_value()
    {
        $SUT = new FindUrls();
        $in = "http://example.com http://example.com";
        $expected = array("http://example.com");
        $result = $SUT->__invoke($in);
        $this->assertEquals($expected, $result);
    }
}
