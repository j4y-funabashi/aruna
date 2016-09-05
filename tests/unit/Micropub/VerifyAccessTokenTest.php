<?php

namespace Test;

use Aruna\Micropub\AccessToken;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

/**
 * Class AccessTokenTest
 * @author yourname
 */
class AccessTokenTest extends UnitTest
{

    public function setUp()
    {
        $this->token_url = "https:://example.com/token";
        $this->me_url = "https:://example.com/";
        $this->SUT = new AccessToken(
            $this->getGuzzle("me=".$this->me_url."&scope=post"),
            $this->token_url,
            $this->me_url
        );
    }

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
    public function it_throws_exception_if_auth_code_is_null()
    {
        $this->SUT->getTokenFromAuthCode(null);
    }

    /**
     * @test
     */
    public function it_returns_token_endpoint_response_body()
    {
        $auth_code = "xxxxxx";
        $result = $this->SUT->getTokenFromAuthCode($auth_code);
        $this->assertEquals($this->me_url, $result['me']);
        $this->assertEquals("post", $result['scope']);
    }
}
