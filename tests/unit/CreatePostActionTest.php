<?php

namespace Test;

use Aruna\CreatePostAction;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;

/**
 * Class CreatePostActionTest
 * @author yourname
 */
class CreatePostActionTest extends UnitTest
{
    public function setUp()
    {
        $this->log = new \Monolog\Logger("test");
        $this->log->pushHandler(new \Monolog\Handler\TestHandler());
        $this->handler = $this->prophesize("\Aruna\CreateEntryHandler");
        $this->token = $this->prophesize("\Aruna\AccessToken");
        $this->responder = $this->prophesize("\Aruna\CreatePostResponder");

        $this->SUT = new CreatePostAction(
            $this->log,
            $this->handler->reveal(),
            $this->token->reveal(),
            $this->responder->reveal()
        );
    }

    /**
     * @test
     */
    public function it_calls_unauthorized_method_on_responder_when_token_is_invalid()
    {
        $error_message = "test";
        $this->token->getTokenFromAuthCode(Argument::cetera())
            ->willThrow(new \Exception($error_message));

        $request = new Request();
        $response = $this->SUT->__invoke($request);

        $this->responder->unauthorized($error_message)
            ->shouldBeCalled();
    }
}
