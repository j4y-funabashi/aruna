<?php

namespace Test;

use Aruna\WebMention\WebMentionHandler;

/**
 * Class WebMentionHandlerTest
 * @author yourname
 */
class WebMentionHandlerTest extends UnitTest
{

    public function setUp()
    {
        $this->eventWriter = $this->prophesize("\Aruna\EventWriter");
        $this->SUT = new WebMentionHandler($this->eventWriter->reveal());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_exception_if_target_is_null()
    {
        $mention = ['source' => 'test'];
        $this->SUT->recieve($mention);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_exception_if_source_is_null()
    {
        $mention = ['target' => 'test'];
        $this->SUT->recieve($mention);
    }

    /**
    * @test
    */
    public function it_passes_mention_to_writer()
    {
        $mention = ['target' => 'test', 'source' => 'test'];
        $this->SUT->recieve($mention);
        $this->eventWriter->save($mention)
            ->shouldBeCalled();
    }
}
