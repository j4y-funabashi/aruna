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
        $http
    ) {
        $this->log = $logger;
        $this->handler = $handler;
        $this->http = $http;
    }

    public function createPost(Application $app, Request $request)
    {
        $this->log->info(__METHOD__);

        // VERIFY ACCESS TOKEN
        $response = $this->http->request(
            'GET',
            $this->token_url,
            [
                'headers' => [
                    'Authorization' => $request->headers->get('Authorization'),
                    'Content-type' =>  'application/x-www-form-urlencoded'
                ]
            ]
        );
        parse_str($response->getBody(), $body);
        if ($body['me'] !== "http://j4y.co/" || $body['scope'] !== "post") {
            return new Response("", 403);
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
        $this->log->info("Post Created: ".$url);
        return new Response("", Response::HTTP_ACCEPTED, ['Location' => $url]);
    }

    public function form(Application $app, Request $request)
    {
        return $app['twig']->render(
            'micropub.html',
            [
                'current_date' => date('c')
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
