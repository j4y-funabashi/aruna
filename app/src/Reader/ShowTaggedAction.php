<?php

namespace Aruna\Reader;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class ShowTaggedAction
{
    public function __construct(
        $handler,
        $responder
    ) {
        $this->handler = $handler;
        $this->responder = $responder;
    }

    public function __invoke(Application $app, Request $request, $tag)
    {
        $rpp = $app['rpp'];
        $this->responder->setPayload(
            $this->handler->handle(
                new ShowTaggedCommand(
                    array(
                        "tag" => $tag,
                        "rpp" => $rpp,
                        "page" => $request->query->get("page")
                    )
                )
            )
        );
        return $this->responder->__invoke();
    }
}
