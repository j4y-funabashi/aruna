<?php

namespace Test;

use Aruna\Webmention\WebmentionNotification;
use Aruna\PostViewModel;

class WebmentionNotificationTest extends UnitTest
{

    /**
     * @test
     */
    public function it_prints_message_for_generic_mentions()
    {
        $SUT = new WebmentionNotification();
        $mention = new PostViewModel(json_decode($this->loadJsonFixture("post_note"), true));
        $post = new PostViewModel(json_decode($this->loadJsonFixture("post_photo"), true));

        $result = $SUT->build($post, $mention);
        $expected = 'Joe Bloggs linked to your photo "Go Ape, Newcastle" [http://j4y.co/p/20160525153645_5745b87d52719][http://j4y.co/p/20160525153645_5745b87d52719]';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_prints_message_for_likes()
    {
        $SUT = new WebmentionNotification();
        $mention = new PostViewModel(json_decode($this->loadJsonFixture("post_like"), true));
        $post = new PostViewModel(json_decode($this->loadJsonFixture("post_photo"), true));

        $result = $SUT->build($post, $mention);
        $expected = 'Joe Bloggs liked your photo "Go Ape, Newcastle" [http://j4y.co/p/20160525153645_5745b87d52719][http://j4y.co/p/20160525153645_5745b87d52mmds]';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_prints_message_for_replies()
    {
        $SUT = new WebmentionNotification();
        $mention = new PostViewModel(json_decode($this->loadJsonFixture("post_reply"), true));
        $post = new PostViewModel(json_decode($this->loadJsonFixture("post_photo"), true));

        $result = $SUT->build($post, $mention);
        $expected = 'Joe Bloggs commented on your photo "Go Ape, Newcastle" [http://j4y.co/p/20160525153645_5745b87d52719][http://j4y.co/p/20160525153645_5745b87d52mmds]';
        $this->assertEquals($expected, $result);
    }
}
