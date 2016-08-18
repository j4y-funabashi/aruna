<?php

namespace CLI;

class ForeverHandler
{

    private $forever;

    public function __construct($forever)
    {
        $this->forever = $forever;

        pcntl_signal(SIGTERM, function ($signo) {
            $this->forever = false;
        });
    }

    public function isForever()
    {
        pcntl_signal_dispatch();
        return $this->forever;
    }
}
