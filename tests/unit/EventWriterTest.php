<?php

namespace Test;

use Aruna\Event;
use Aruna\EventWriter;

/**
 * Class EventWriterTest
 * @author yourname
 */
class EventWriterTest extends UnitTest
{
    public function setUp()
    {
        $this->filesystem = $this->prophesize("\League\Flysystem\Filesystem");
        $this->SUT = new EventWriter(
            $this->filesystem->reveal()
        );
    }

    /**
     * @test
     */
    public function it_does_something_awesome()
    {
        $event = new Event(["blah" => 1]);
        $this->filesystem->write($event->getUid().".json", json_encode($event))
            ->shouldBeCalled();
        $this->SUT->save($event);
    }
}
