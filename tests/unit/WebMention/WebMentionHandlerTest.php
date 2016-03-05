<?php

namespace Test;

use Aruna\WebMention\WebMentionHandler;

/**
 * Class WebMentionHandlerTest
 * @author yourname
 */
class WebMentionHandlerTest extends UnitTest
{

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_exception_if_target_is_null()
    {
        $SUT = new WebMentionHandler();
        $mention = ['source' => 'test'];
        $SUT->recieve($mention);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_exception_if_source_is_null()
    {
        $SUT = new WebMentionHandler();
        $mention = ['target' => 'test'];
        $SUT->recieve($mention);
    }
}
