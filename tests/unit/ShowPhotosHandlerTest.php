<?php

namespace Test;

use \Aruna\ShowPhotosHandler;
use \Aruna\ShowPhotosCommand;

class ShowPhotosHandlerTest extends UnitTest
{
    public function setUp()
    {
        $this->url_generator = $this->prophesize("Aruna\UrlGenerator");
        $this->postRepository = $this->prophesize("Aruna\PostRepository");
        $this->SUT = new ShowPhotosHandler(
            $this->postRepository->reveal(),
            $this->url_generator->reveal()
        );
    }

    /**
     * @test
     */
    public function it_returns_payload_with_items_from_repository()
    {
        $rpp = 10;
        $page = 22;
        $command = new ShowPhotosCommand(
            array(
                "rpp" => $rpp,
                "page" => $page
            )
        );
        $expected = array();
        $this->postRepository->listByType("photo", $rpp, 210)
            ->willReturn($expected);
        $result = $this->SUT->handle($command);
        $this->assertEquals($expected, $result->get("items"));
    }

}
