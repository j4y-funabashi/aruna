<?php

namespace Test;

use Aruna\Publish\CleanupPhotoUrl;

class CleanupPhotoUrlTest extends UnitTest
{
    public function setUp()
    {
        $media_endpoint = "http://media/";
        $this->SUT = new CleanupPhotoUrl($media_endpoint);
    }

    /**
     * @test
     */
    public function it_does_nothing_if_no_photo_exists()
    {
        $post = [];
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($post, $result);
    }

    /**
     * @test
     */
    public function it_adds_media_endpoint_if_photo_has_no_host()
    {
        $post = [
            "properties" => [
                "photo" => ["test.jpg"]
            ]
        ];
        $expected = [
            "properties" => [
                "photo" => ["http://media/test.jpg"]
            ]
        ];
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }
}
