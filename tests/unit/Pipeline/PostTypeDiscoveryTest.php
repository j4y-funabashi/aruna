<?php

namespace Test;

use Aruna\Pipeline\PostTypeDiscovery;

/**
 * Class PostTypeDiscoveryTest
 * @author yourname
 */
class PostTypeDiscoveryTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new PostTypeDiscovery();
    }

    /**
    * @test
    */
    public function it_defaults_post_type_to_note()
    {
        $post = array();
        $result = $this->SUT->__invoke($post);
        $this->assertArrayHasKey("post_type", $result);
        $this->assertEquals("note", $result['post_type']);
    }

    /**
    * @test
    */
    public function it_discovers_photo_posts()
    {
        $post = array(
            "files" => array(
                "photo" => "test.jpg"
            )
        );
        $result = $this->SUT->__invoke($post);
        $this->assertEquals("photo", $result['post_type']);
    }

    /**
    * @test
    */
    public function it_discovers_bookmark_posts()
    {
        $post = array(
            "bookmark-of" => "www.example.com"
        );
        $result = $this->SUT->__invoke($post);
        $this->assertEquals("bookmark", $result['post_type']);
    }
}
