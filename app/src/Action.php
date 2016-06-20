<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class Action
 * @author yourname
 */
abstract class Action
{
    public function __construct(
        Responder $responder,
        Handler $handler
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
}
