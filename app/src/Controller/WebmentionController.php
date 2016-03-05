<?php

namespace Aruna\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Class WebmentionController
 * @author yourname
 */
class WebmentionController
{
    public function __construct(
        $log,
        $mentionsHandler
    ) {
        $this->log = $log;
        $this->mentionsHandler = $mentionsHandler;
    }

    public function createMention(Application $app, Request $request)
    {
        $this->mentionsHandler->recieve([
            'source' => $request->get("source"),
            'target' => $request->get("target")
        ]);
        return "hello!";
    }
}
