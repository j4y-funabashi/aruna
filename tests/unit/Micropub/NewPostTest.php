<?php

namespace Test;

use Aruna\Micropub\NewPost;
use Aruna\Micropub\UploadedFile;

class NewPostTest extends UnitTest
{

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
        $uploadedFile = new UploadedFile(
            $real_path = '/tmp/test',
            $original_ext = 'jpg',
            $is_readable = true,
            $is_valid = true
        );
        $post = new NewPost(
            ["hello" => "test", "published" => "2016-01-28 10:00:00"],
            [$uploadedFile]
        );
        $result = json_encode($post);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals($result, $post->asJson());
        $this->assertEquals(
            "2016-01-28T10:00:00+00:00",
            json_decode($result, true)["published"]
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
    public function it_creates_a_filepath_from_the_posts_uid()
    {
        $post = new NewPost([]);
        $result = $post->getFilePath();
        $this->assertRegExp("/\d{4}\/\d{14}_\w{13}/", $result);
    }
}
