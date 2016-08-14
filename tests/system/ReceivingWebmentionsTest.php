<?php

namespace Test;

use Symfony\Component\HttpFoundation\Request;

class ReceivingWebmentionsTest extends SystemTest
{

    /**
     * @test
     */
    public function it_returns_400_when_source_and_target_match()
    {
        $request = array(
            "source" => "http://example.com",
            "target" => "http://example.com"
        );
        $request = new Request($query = [], $request);
        $this->SUT = $this->app['action.receive_webmention'];
        $result = $this->SUT->__invoke($request);
        $this->assertEquals(400, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_202_when_mention_is_valid()
    {
        $request = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co"
        );
        $request = new Request($query = [], $request);
        $this->SUT = $this->app['action.receive_webmention'];
        $result = $this->SUT->__invoke($request);
        $this->assertEquals(202, $result->getStatusCode());
    }
}
