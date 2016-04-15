<?php

namespace Aruna;

/**
 * Class CommandBus
 * @author yourname
 */
class CommandBus implements Handler
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function handle($command)
    {
        $klass = str_replace("Command", "", get_class($command));
        $klass = "handler." . strtolower(substr($klass, strrpos($klass, '\\')+1));
        return $this->app[$klass]->handle($command);
    }
}
