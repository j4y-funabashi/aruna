<?php

namespace Test;

use Aruna\Webmention\Event;
use Aruna\Webmention\EventWriter;

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
    public function it_tells_filesystem_to_write_json_version_of_event()
    {
        $event = new Event(["blah" => 1]);
        $this->filesystem->write($event->getUid().".json", json_encode($event))
            ->shouldBeCalled();
        $this->SUT->save($event);
    }
}
