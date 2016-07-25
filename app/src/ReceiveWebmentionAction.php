<?php

namespace Aruna;

class ReceiveWebmentionAction extends Action
{
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
