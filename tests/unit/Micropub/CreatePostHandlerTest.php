<?php

namespace Test;

use Prophecy\Argument;

use Aruna\Micropub\CreatePostHandler;
use Aruna\Micropub\CreatePostCommand;
use Aruna\Response\Unauthorized;
use Aruna\Response\ServerError;
use Aruna\Response\OK;

class CreatePostHandlerTest extends UnitTest
{
    public function setUp()
    {
        $this->postRepository = $this->prophesize("\Aruna\Micropub\PostRepositoryWriter");
        $this->accessToken = $this->prophesize("\Aruna\Micropub\AccessToken");

        $this->access_token = null;
        $this->files = [];
        $this->entry = [];

        $this->SUT = new CreatePostHandler(
            $this->postRepository->reveal(),
            $this->accessToken->reveal()
        );
    }

    /**
     * @test
     */
    public function it_returns_unauthorized_for_invalid_access_token()
    {
        $this->accessToken->getTokenFromAuthCode(null)
            ->willThrow(new \Exception());
        $result = $this->SUT->handle($this->getCommand());
        $this->assertInstanceOf(Unauthorized::class, $result);
    }

    /**
     * @test
     */
    public function it_returns_server_error_when_save_fails()
    {
        $this->postRepository->save(Argument::cetera())
            ->willThrow(new \Exception());
        $result = $this->SUT->handle($this->getCommand());
        $this->assertInstanceOf(ServerError::class, $result);
    }

    /**
     * @test
     */
    public function it_returns_ok_for_valid_posts()
    {
        $result = $this->SUT->handle($this->getCommand());
        $this->assertInstanceOf(OK::class, $result);
    }

    private function getCommand()
    {
        return new CreatePostCommand(
            $this->entry,
            $this->files,
            $this->access_token
        );
    }
}
