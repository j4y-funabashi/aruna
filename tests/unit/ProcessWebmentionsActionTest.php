<?php

namespace Test;

use Prophecy\Argument;
use Aruna\ProcessWebmentionsAction;

class ProcessWebmentionsActionTest extends UnitTest
{
    public function setUp()
    {
        $this->log = $this->prophesize("\Monolog\Logger");
        $this->eventStore = $this->prophesize("\Aruna\EventStore");
        $this->handler = $this->prophesize("\Aruna\ProcessWebmentionsHandler");
        $this->SUT = new ProcessWebmentionsAction(
            $this->log->reveal(),
            $this->eventStore->reveal(),
            $this->handler->reveal()
        );

        //stubs
        $this->files = array(
            array("path" => "/test")
        );
        $this->eventStore->findByExtension('webmentions', 'json', 10)
            ->willReturn($this->files);
        $this->eventStore->readContents('/test')
            ->willReturn('{"test":"123"}');
        $this->eventStore->delete("/test")
            ->willReturn(null);
    }

    /**
     * @test
     */
    public function it_passes_files_to_handler()
    {
        $this->handler->handle($this->files[0])
            ->shouldBeCalled();
        $this->SUT->__invoke();
    }

    /**
     * @test
     */
    public function it_removes_files_once_they_have_been_handled()
    {
        $this->eventStore->delete("/test")
            ->shouldBeCalled();
        $this->SUT->__invoke();
    }

    /**
     * @test
     */
    public function it_logs_handler_exceptions()
    {
        $this->handler->handle($this->files[0])
            ->willThrow(new \Exception());
        $this->log->error(Argument::cetera())
            ->shouldBeCalled();
        $this->SUT->__invoke();
    }
}
