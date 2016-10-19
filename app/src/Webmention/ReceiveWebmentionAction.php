<?php

namespace Aruna\Webmention;

use Symfony\Component\HttpFoundation\Request;

class ReceiveWebmentionAction
{
    public function __construct(
        $responder,
        $handler
    ) {
        $this->responder = $responder;
        $this->handler = $handler;
    }

    public function __invoke(Request $request)
    {

        $this->responder->setPayload(
            $this->handler->handle(
                $this->getCommand($request)
            )
        );

        return $this->responder->__invoke();
    }

    public function getCommand($request)
    {
        return new ReceiveWebmentionCommand(
            array(
                "source" => $request->get("source"),
                "target" => $request->get("target")
            )
        );
    }
}
