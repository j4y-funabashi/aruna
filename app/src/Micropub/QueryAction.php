<?php

namespace Aruna\Micropub;

use Symfony\Component\HttpFoundation\Request;

class QueryAction
{
    public function __construct(
        $handler,
        $responder
    ) {
        $this->handler = $handler;
        $this->responder = $responder;
    }

    public function __invoke(Request $request)
    {
        $this->responder->setPayload(
            $this->handler->handle($this->getCommand($request))
        );
        return $this->responder->__invoke();
    }

    private function getCommand($request)
    {
        return new QueryCommand($request->query->all());
    }
}
