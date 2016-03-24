<?php

namespace Aruna;

/**
 * Class ShowMicropubFormAction
 * @author yourname
 */
class ShowMicropubFormAction
{

    public function __construct(
        Responder $responder,
        Handler $handler
    ) {
        $this->responder = $responder;
        $this->handler = $handler;
    }

    public function __invoke()
    {
        $command = new ShowMicropubFormCommand();
        $this->responder->setPayload(
            $this->handler->handle($command)
        );
        return $this->responder->__invoke();
    }
}
