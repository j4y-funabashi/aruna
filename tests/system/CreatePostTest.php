<?php

namespace Test;

use Aruna\Micropub\CreatePostCommand;
use Aruna\Response\Unauthorized;

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
            $access_token = ""
        );
        $result = $SUT->handle($command);

        $expected = sprintf("Invalid access token [%s]", $access_token);
        $this->assertEquals($expected, $result->get("message"));
        $this->assertInstanceOf(Unauthorized::class, $result);
    }
}
