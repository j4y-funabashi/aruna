<?php

namespace Test;

use Aruna\Micropub\CreatePostResponder;
use Symfony\Component\HttpFoundation\Response;
use Aruna\Response\OK;
use Aruna\Response\Unauthorized;
use Aruna\Response\ServerError;

class CreatePostResponderTest extends UnitTest
{
    public function setUp()
    {
        $this->view = null;
        $this->postRenderer = null;
        $this->response = new Response();

        $this->SUT = new CreatePostResponder(
            $this->response,
            $this->view,
            $this->postRenderer
        );
    }

    /**
     * @test
     */
    public function it_returns_ok()
    {
        $this->SUT->setPayload(new OK([]));
        $result = $this->SUT->__invoke();
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_server_error()
    {
        $this->SUT->setPayload(new ServerError(["message" => "test"]));
        $result = $this->SUT->__invoke();
        $this->assertEquals("test", $result->getContent());
        $this->assertEquals(500, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_unauthorized()
    {
        $this->SUT->setPayload(new Unauthorized(["message" => "test"]));
        $result = $this->SUT->__invoke();
        $this->assertEquals("test", $result->getContent());
        $this->assertEquals(401, $result->getStatusCode());
    }
}
