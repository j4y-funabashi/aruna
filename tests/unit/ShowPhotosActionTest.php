<?php

namespace Test;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Aruna\ShowPhotosAction;

class ShowPhotosActionTest extends UnitTest
{

    /**
     * @test
     */
    public function it_returns_responders_response()
    {
        $request = new Request();
        $app = new Application();
        $app['rpp'] = 0;
        $handler = $this->prophesize("Aruna\Handler");
        $responder = $this->prophesize("Aruna\Responder");

        $this->SUT = new ShowPhotosAction(
            $handler,
            $responder
        );
        $this->SUT->__invoke(
            $app,
            $request
        );
    }
}
