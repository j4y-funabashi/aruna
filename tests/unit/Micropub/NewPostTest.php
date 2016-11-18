<?php

namespace Test;

use Aruna\Micropub\NewPost;
use Aruna\Micropub\UploadedFile;

class NewPostTest extends UnitTest
{
    public function setUp()
    {
        $this->config = [
            "uid" => "test123",
            "content" => "this is a test",
            "category" => ["test1", "test2"],
            "published" => "2016-01-28 15:00:02"
        ];
        $this->uploadedFile = new UploadedFile(
            $real_path = '/tmp/test',
            $original_ext = 'jpg',
            $is_readable = true,
            $is_valid = true
        );
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage test is not a valid date
     */
    public function it_throws_exception_for_invalid_dates()
    {
        $post = [
            "published" => "test"
        ];
        $result = new NewPost($post);
    }

    /**
     * @test
     */
    public function it_can_be_json_encoded_when_valid()
    {
        $post = new NewPost(
            array_merge(
                $this->config,
                ["photo" => "2016/test123.jpg"]
            )
        );
        $this->assertJsonStringEqualsJsonFile(
            "tests/fixtures/new_post_with_file.json",
            $post->asJson()
        );
    }

    /**
     * @test
     */
    public function it_adds_h_property_if_it_does_not_exist()
    {
        $post = new NewPost([]);
        $result = json_decode(json_encode($post), true);
        $this->assertArrayHasKey("h", $result);
        $this->assertEquals("entry", $result["h"]);
    }

    /**
     * @test
     */
    public function it_removes_access_token()
    {
        $post = new NewPost(["access_token" => "test"]);
        $result = json_decode(json_encode($post), true);
        $this->assertFalse(isset($result["access_token"]));
    }

    /**
     * @test
     */
    public function it_creates_a_filepath()
    {
        $post = new NewPost($this->config);
        $result = $post->getFilePath();
        $expected = "2016/test123";
        $this->assertEquals($expected, $result);
    }
}
