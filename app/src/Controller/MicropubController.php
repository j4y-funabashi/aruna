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
    private $token_url = "https://tokens.indieauth.com/token";

    public function __construct(
        $logger,
        $handler,
        $accessToken,
        $urlGenerator
    ) {
        $this->log = $logger;
        $this->handler = $handler;
        $this->accessToken = $accessToken;
        $this->urlGenerator = $urlGenerator;
    }

    public function createPost(Request $request)
    {
        $this->log->info(__METHOD__);

        $access_token = (null == $request->headers->get('Authorization'))
            ? $request->request->get('access_token')
            : $request->headers->get('Authorization');

        $this->log->info($access_token);

        try {
            $token = $this->accessToken->getTokenFromAuthCode($access_token);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        $entry = $this->buildEntryArray($request);
        $files = $this->buildFilesArray($request);
        $command = new \Aruna\CreateEntryCommand($entry, $files);
        $newEntry = $this->handler->handle($command);

        $url = $this->urlGenerator->generate(
            'post',
            array('post_id' => $newEntry->getPostId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->log->info("Post Created: ".$url);

        return new Response("", Response::HTTP_ACCEPTED, ['Location' => $url]);
    }

    public function form(Application $app, Request $request)
    {
        return $app['twig']->render(
            'micropub.html',
            [
                'current_date' => date('c'),
                'access_token' => $app['session']->get('access_token')
            ]
        );
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
