<?php

namespace Test;

use Aruna\Micropub\NewPost;

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
        $post = new NewPost([], ["photo" => ["original_ext" => "jpg"]]);
        $result = json_encode($post);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals($result, $post->asJson());
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
    public function it_removes_access_token_from_properties()
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
