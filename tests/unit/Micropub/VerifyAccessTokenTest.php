<?php

namespace Test;

use Aruna\Micropub\VerifyAccessToken;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

/**
 * Class VerifyAccessTokenTest
 * @author yourname
 */
class VerifyAccessTokenTest extends UnitTest
{

    public function setUp()
    {
        $this->token_url = "https:://example.com/token";
        $this->me_url = "https:://example.com/";
        $this->client_id = "https://ownyourgram.com";
        $good_response = sprintf(
            "me=%s&client_id=%s&scope=post&issued_at=1399155608&nonce=501884823",
            $this->me_url,
            $this->client_id
        );
        $this->SUT = new VerifyAccessToken(
            $this->getGuzzle(200, $good_response),
            $this->token_url,
            $this->me_url
        );
    }

    private function getGuzzle($response_code, $response_body)
    {
        $mock = new MockHandler([
            new Response($response_code, [], $response_body)
        ]);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Missing Authorization Header
     */
    public function it_throws_exception_if_auth_code_is_null()
    {
        $this->SUT->getTokenFromAuthCode(null);
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Token endpoint returned with status 400
     */
    public function it_throws_exception_token_endpoint_does_not_return_200()
    {
        $this->SUT = new VerifyAccessToken(
            $this->getGuzzle(400, ""),
            $this->token_url,
            $this->me_url
        );
        $this->SUT->getTokenFromAuthCode("test");
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage Me value [test] does not match https:://example.com/
     */
    public function it_throws_exception_if_token_me_value_is_not_correct()
    {
        $response = "me=test&client_id=test&scope=post&issued_at=1399155608&nonce=501884823";
        $this->SUT = new VerifyAccessToken(
            $this->getGuzzle(200, $response),
            $this->token_url,
            $this->me_url
        );
        $this->SUT->getTokenFromAuthCode("test");
    }

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage scope is not post
     */
    public function it_throws_exception_if_token_scope_is_not_post()
    {
        $response = "me=https:://example.com/&client_id=test&scope=test&issued_at=1399155608&nonce=501884823";
        $this->SUT = new VerifyAccessToken(
            $this->getGuzzle(200, $response),
            $this->token_url,
            $this->me_url
        );
        $this->SUT->getTokenFromAuthCode("test");
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
