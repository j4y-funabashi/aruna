<?php

namespace Test;

use Aruna\Controller\MicropubController;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;

/**
 * Class MicropubControllerTest
 * @author yourname
 */
class MicropubControllerTest extends UnitTest
{
    public function setUp()
    {
        $this->log = new \Monolog\Logger("test");
        $this->log->pushHandler(new \Monolog\Handler\TestHandler());
        $this->handler = $this->prophesize("\Aruna\CreateEntryHandler");
        $this->token = $this->prophesize("\Aruna\AccessToken");
        $this->urlGenerator = $this->prophesize("\Symfony\Component\Routing\Generator\UrlGenerator");
        $this->SUT = new MicropubController(
            $this->log,
            $this->handler->reveal(),
            $this->token->reveal(),
            $this->urlGenerator->reveal()
        );
    }

    /**
     * @test
     */
    public function it_returns_unauthorized_response_if_token_throws_exception()
    {
        $this->token->getTokenFromAuthCode(Argument::cetera())
            ->willThrow(new \Exception("blah"));

        $request = new Request();
        $response = $this->SUT->createPost($request);

        $this->assertEquals("blah", $response->getContent());
        $this->assertEquals(401, $response->getStatusCode());
    }
}
