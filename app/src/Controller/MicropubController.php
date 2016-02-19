<?php

namespace Aruna\Controller;

use Symfony\Component\HttpFoundation\Request;
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
        $this->logger = $logger;
        $this->handler = $handler;
    }

    public function createPost(Application $app, Request $request)
    {
        $entry = $this->buildEntryArray($request);
        $files = $this->buildFilesArray($request);
        $command = new \Aruna\CreateEntryCommand($entry, $files);
        $newEntry = $this->handler->handle($command);
        return $newEntry->getFilePath();
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
            $files[$file_key] = $uploadedFile;
        }
        return $files;
    }
}
