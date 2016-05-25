<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class ShowPhotosAction
{
    public function __construct(
        $handler,
        $responder
    ) {
        $this->handler = $handler;
        $this->responder = $responder;
    }

    public function __invoke(Application $app, Request $request)
    {
        $rpp = $app['rpp'];
        $this->responder->setPayload(
            $this->handler->getLatestPhotos(
                $rpp,
                $request->query->get("page")
            )
        );
        return $this->responder->__invoke();
    }
}
