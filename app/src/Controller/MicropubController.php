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
        $logger
    ) {
        $this->logger = $logger;
    }

    public function handle(Application $app, Request $request)
    {
        $TMP_FILES_DIR = "/tmp/aruna";
        $adapter = new \League\Flysystem\Adapter\Local("/tmp/aruna");
        $filesystem = new \League\Flysystem\Filesystem($adapter);
        $noteStore = new \Aruna\EntryRepository($filesystem);
        $handler = new \Aruna\CreateEntryHandler($noteStore);

        // build $entry array from request parameters
        $entry = [];
        foreach ($request->request->all() as $key => $value) {
            $entry[$key] = $value;
        }
        // build files array
        $files = [];
        foreach ($request->files as $file_key => $uploadedFile) {
            $files[$file_key] = $uploadedFile;
        }

        $command = new \Aruna\CreateEntryCommand($entry, $files);
        $newEntry = $handler->handle($command);

        return $newEntry->getFilePath();
    }
}
