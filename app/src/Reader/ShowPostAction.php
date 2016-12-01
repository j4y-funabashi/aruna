<?php

namespace Aruna\Reader;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class ShowPostAction
{
    public function __construct(
        $handler,
        $responder
    ) {
        $this->handler = $handler;
        $this->responder = $responder;
    }

    public function __invoke(Application $app, Request $request, $post_id)
    {
        $this->responder->setPayload(
            $this->handler->handle(
                new ShowPostCommand(
                    array(
                        "post_id" => $post_id
                    )
                )
            )
        );
        return $this->responder->__invoke();
    }
}
