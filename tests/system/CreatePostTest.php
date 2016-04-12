<?php

namespace Test;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CreatePostTest
 * @author yourname
 */
class CreatePostTest extends SystemTest
{
    /**
    * @test
    */
    public function it_creates_a_post_if_access_token_is_valid()
    {
        $access_token = $this->prophesize("\Aruna\AccessToken");
        $this->app['access_token'] = $access_token->reveal();
        $SUT = $this->app['action.create_post'];

        $now = (new \DateTimeImmutable())->format("c");
        $post = [
            "h" => "entry",
            "published" => $now,
            "content" => "test1",
            "category" => "cat, kitty"
        ];
        $files = [
            "photo" => new UploadedFile(
                $path = __DIR__ . "/test.jpg",
                $originalName = "chicken.jpg",
                $mimeType = null,
                $size = null,
                $error = null,
                $test = true
            )
        ];
        $request = new Request([], $post, [], [], $files);

        $response = $SUT->__invoke($request);

        $post = json_decode($response->getContent(), true);
        $json_path = getenv("ROOT_DIR")."/posts/".(new \DateTimeImmutable($post['published']))->format("Y")
            . "/" . $post['uid'].".json";
        $jpg_path = getenv("ROOT_DIR")."/posts/".(new \DateTimeImmutable($post['published']))->format("Y")
            . "/" . $post['uid'].".jpg";

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertFileExists($jpg_path);
        $this->assertFileExists($json_path);
        $this->assertEquals($response->getContent(), file_get_contents($json_path));
    }

    /**
    * @test
    */
    public function it_returns_unauthorized_401_if_access_token_is_invalid()
    {
        $SUT = $this->app['action.create_post'];

        $post = ["h" => "entry"];
        $request = new Request([], $post, [], [], []);

        $response = $SUT->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
    }
}
