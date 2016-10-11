<?php

namespace Aruna\Micropub;

use League\Flysystem\FileExistsException;
use RuntimeException;

/**
 * Class PostRepositoryWriter
 * @author yourname
 */
class PostRepositoryWriter
{
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(NewPost $entry, $files)
    {
        foreach ($files as $uploadedFile) {
            try {
                $stream = fopen($uploadedFile->getRealPath(), 'rb');
                if (!$stream) {
                    $m = "Could not open file ".$uploadedFile->getRealPath();
                    throw new \RuntimeException($m);
                }
                $this->filesystem->writeStream(
                    $entry->getFilePath().".".$uploadedFile->getExtension(),
                    $stream
                );
            } catch (FileExistsException $e) {
                throw new RuntimeException($e->getMessage());
            }
        }

        try {
            // json file
            $this->filesystem->write(
                $entry->getFilePath().".json",
                $entry->asJson()
            );
        } catch (FileExistsException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
