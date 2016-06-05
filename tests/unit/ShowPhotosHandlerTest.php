<?php

namespace Test;

use \Aruna\ShowPhotosHandler;

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
    public function it_returns_latest_photos()
    {
        $rpp = 10;
        $page = 22;
        $expected = array();
        $this->postRepository->listByType("photo", $rpp, 210)
            ->willReturn($expected);
        $result = $this->SUT->getLatestPhotos($rpp, $page);
        $this->assertEquals($expected, $result->get("items"));
    }

}
