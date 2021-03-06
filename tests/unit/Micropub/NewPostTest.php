<?php

namespace Test;

use Aruna\Micropub\NewPost;
use Aruna\Micropub\UploadedFile;
use DateTimeImmutable;

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
        $this->now = new DateTimeImmutable("2016-01-28 15:00:02");
        $this->uploadedFile = new UploadedFile(
            $real_path = '/tmp/test',
            $original_ext = 'jpg',
            $is_readable = true,
            $is_valid = true
        );
    }

    /**
     * @test
     */
    public function it_converts_microformats_formatted_posts()
    {
        $this->config = [
            "type" => ["h-event"],
            "properties" => [
                "uid" => "test123",
                "content" => "this is a test",
                "category" => ["test1", "test2"],
                "published" => "2016-01-28 15:00:02",
                "photo" => "2016/test123.jpg"
            ]
        ];

        $post = new NewPost(
            $this->config,
            new \DateTimeImmutable("2016-01-28 10:00:00")
        );
        $this->assertJsonStringEqualsJsonString(
            '{"eventType": "PostCreated","eventVersion": "20160128100000","eventID": "test123","eventData": {"type": ["h-event"], "properties": {"uid":["test123"],"content":["this is a test"],"category":["test1","test2"],"published":["2016-01-28T15:00:02+00:00"],"h":["event"],"photo":["2016\/test123.jpg"]}}}',
            $post->asJson()
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
            ),
            new \DateTimeImmutable("2016-01-28 10:00:00")
        );
        $this->assertJsonStringEqualsJsonFile(
            "tests/fixtures/new_post_with_file.json",
            $post->asJson()
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_rejects_update_events_with_no_action()
    {
        $post = new NewPost(["action" => "update"]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_rejects_update_events_where_action_is_not_an_array()
    {
        $post = new NewPost(["action" => "update", "replace" => "1"]);
    }

    /**
     * @test
     */
    public function it_adds_h_property_if_it_does_not_exist()
    {
        $post = new NewPost([]);
        $result = json_decode(json_encode($post), true)["eventData"];
        $this->assertEquals("h-entry", $result["type"][0]);
    }

    /**
     * @test
     */
    public function it_removes_access_token()
    {
        $post = new NewPost(["access_token" => "test"]);
        $result = json_decode(json_encode($post), true)["eventData"];
        $this->assertFalse(isset($result["access_token"]));
    }

    /**
     * @test
     */
    public function it_creates_a_filepath()
    {
        $post = new NewPost($this->config, $this->now);
        $result = $post->getFilePath();
        $expected = "2016/test123";
        $this->assertEquals($expected, $result);
    }
}
