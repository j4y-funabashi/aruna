<?php

namespace Aruna\Webmention;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class ListWebmentionsAction
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
            $this->handler->handle(
                new ListWebmentionsCommand(
                    array(
                        "rpp" => $rpp,
                        "page" => $request->query->get("page")
                    )
                )
            )
        );
        return $this->responder->__invoke();
    }
}
