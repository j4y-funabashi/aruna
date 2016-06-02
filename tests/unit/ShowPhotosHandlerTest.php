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
        $url_generator = null;
        $postRepository = $this->prophesize("Aruna\PostRepository");
        $this->SUT = new ShowPhotosHandler(
            $postRepository,
            $url_generator
        );

        $rpp = 10;
        $page = 22;
        $this->SUT->getLatestPhotos(
            $rpp,
            $page
        );
    }

}
