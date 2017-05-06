<?php

namespace Test;

use \Aruna\Publish\ProcessingPipelineFactory;
use \Aruna\Publish\EventProcessor;

class EventProcessorTest extends SystemTest
{

    /**
     * @test
     */
    public function it_awesome()
    {
        $this->SUT = new EventProcessor(
            new ProcessingPipelineFactory($this->app)
        );
        $event = [
            "type" => "WebmentionReceived",
            "data" => [
                "source" => "https://j4y.co/p/20170506185314_590e1b9a3faf9",
                "target" => "https://j4y.co/p/20170506185225_590e1b69240f8",
                "uid" => "test_WebmentionReceived_source"
            ]
        ];

        $result = $this->SUT->__invoke($event);

        $this->assertArrayHasKey("mention_source_html", $result);
        $this->assertEquals(false, $result["error"]);
        $this->assertEquals(true, $result["valid"]);
    }
}
