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
        $event_id = $this->mentionsHandler->recieve([
            'source' => $request->get("source"),
            'target' => $request->get("target")
        ]);

        $url = $app['url_generator']->generate(
            'webmention',
            ['mention_id' => $event_id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new Response($url, Response::HTTP_ACCEPTED);
    }

    public function view(Application $app, Request $request, $mention_id)
    {
        $mention = $this->mentionsHandler->findById($mention_id);
        return $app['twig']->render(
            'mention.html',
            [
                'mention' => $mention
            ]
        );
    }
}
