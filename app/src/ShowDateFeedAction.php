<?php

namespace Aruna;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShowDateFeedAction
 * @author yourname
 */
class ShowDateFeedAction extends Action
{

    public function __invoke(Request $request, $year, $month, $day)
    {

        $command = new ShowDateFeedCommand($year, $month, $day);
        $this->responder->setPayload(
            $this->handler->handle($command)
        );

        return $this->responder->__invoke();
    }
}
