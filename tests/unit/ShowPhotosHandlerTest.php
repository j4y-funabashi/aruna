<?php

namespace Test;

use \Aruna\ShowPhotosHandler;

class ShowPhotosHandlerTest extends UnitTest
{

    /**
     * @test
     */
    public function it_returns_latest_photos()
    {
        $postRepository = null;
        $url_generator = $this->prophesize(
            "Aruna\UrlGenerator"
        );
        $postRepository = $this->prophesize(
            "Aruna\PostRepository"
        );
        $this->SUT = new ShowPhotosHandler(
            $postRepository->reveal(),
            $url_generator->reveal()
        );

        $rpp = 10;
        $page = 22;

        $postRepository->listByType("photo", $rpp, 210)
            ->shouldBeCalled()
            ->willReturn(array());
        $url_generator->generate(
            "photos",
            array("page" => 23)
        )->shouldBeCalled();

        $this->SUT->getLatestPhotos(
            $rpp,
            $page
        );
    }

}
