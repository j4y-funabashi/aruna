<?php

namespace Test;

use Aruna\Micropub\CreatePostCommand;
use Aruna\Response\Unauthorized;
use Aruna\Response\OK;
use Aruna\Micropub\UploadedFile;
use Aruna\Micropub\NewPost;

class CreatePostTest extends SystemTest
{

    /**
     * @test
     */
    public function it_returns_unauthorized_when_token_is_invalid()
    {
        $SUT = $this->app['create_post.handler'];

        $command = new CreatePostCommand(
            $post = [],
            $files = [],
            $access_token = "Bearer xxx"
        );
        $result = $SUT->handle($command);

        $expected = sprintf("Invalid access token [%s]", $access_token);
        $this->assertEquals($expected, $result->get("message"));
        $this->assertInstanceOf(Unauthorized::class, $result);
    }

    /**
     * @test
     */
    public function it_creates_a_post_with_file_when_token_is_valid()
    {
        $fake_token = $this->prophesize("\Aruna\Micropub\VerifyAccessToken");
        $this->app['access_token'] = $fake_token->reveal();
        $SUT = $this->app['create_post.handler'];

        $command = new CreatePostCommand(
            $post = ["test" => 1],
            $files = [
                new UploadedFile(
                    __DIR__."/test.jpg",
                    "jpg",
                    true,
                    true
                )
            ],
            $access_token = "Bearer xxx"
        );
        $result = $SUT->handle($command);
        $this->assertInstanceOf(OK::class, $result);
        $this->assertInstanceOf(NewPost::class, $result->get("items")[0]);
    }
}
