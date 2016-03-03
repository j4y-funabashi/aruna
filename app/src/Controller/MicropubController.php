<?php

namespace Aruna\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Class MicropubController
 * @author yourname
 */
class MicropubController
{

    public function __construct(
        $logger,
        $handler
    ) {
        $this->log = $logger;
        $this->handler = $handler;
    }

    public function createPost(Application $app, Request $request)
    {
        if (null === $app['session']->get('user')) {
            return new Response("", Response::HTTP_UNAUTHORIZED);
        }

        $entry = $this->buildEntryArray($request);
        $files = $this->buildFilesArray($request);
        $command = new \Aruna\CreateEntryCommand($entry, $files);
        $newEntry = $this->handler->handle($command);

        $url = $app['url_generator']->generate(
            'post',
            array('post_id' => $newEntry->getPostId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new Response("", Response::HTTP_ACCEPTED, ['Location' => $url]);
    }

    public function form(Application $app, Request $request)
    {
        return $app['twig']->render('micropub.html');
    }

    private function buildEntryArray($request)
    {
        $entry = [];
        foreach ($request->request->all() as $key => $value) {
            $entry[$key] = $value;
        }
        return $entry;
    }

    private function buildFilesArray($request)
    {
        $files = [];
        foreach ($request->files as $file_key => $uploadedFile) {
            if (false === $uploadedFile->isValid()) {
                throw new \RuntimeException("Upload Error: (".$uploadedFile->getError().")");
            }
            $files[$file_key] = $uploadedFile;
        }
        return $files;
    }
}
