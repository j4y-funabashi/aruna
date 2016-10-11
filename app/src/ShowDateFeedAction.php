<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShowDateFeedAction
 * @author yourname
 */
class ShowDateFeedAction
{
    public function __construct(
        Responder $responder,
        Handler $handler
    ) {
        $this->responder = $responder;
        $this->handler = $handler;
    }

    public function __invoke(Request $request, $year, $month, $day)
    {

        $command = new ShowDateFeedCommand($year, $month, $day);
        $this->responder->setPayload(
            $this->handler->handle($command)
        );

        return $this->responder->__invoke();
    }
}
