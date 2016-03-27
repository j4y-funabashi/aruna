<?php

namespace Aruna;

/**
 * Class ShowMicropubFormAction
 * @author yourname
 */
class ShowMicropubFormAction extends Action
{
    protected function getCommand($request)
    {
        return new ShowMicropubFormCommand();
    }
}
