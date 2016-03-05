<?php

namespace Test;

use Prophecy\Argument;
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
        $this->SUT->recieve(['target' => 'test', 'source' => 'test']);
        $this->eventWriter->save(Argument::type("Aruna\Event"))
            ->shouldBeCalled();
    }
}
