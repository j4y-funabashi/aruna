<?php

namespace Aruna;

use League\Flysystem\FileExistsException;
use RuntimeException;

/**
 * Class PostRepository
 * @author yourname
 */
class PostRepository
{
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(Post $entry, $files)
    {
        foreach ($files as $uploadedFile) {
            try {
                if ($uploadedFile->isReadable() === false) {
                    $m = "Could not read file ".$uploadedFile->getRealPath();
                    throw new \RuntimeException($m);
                }
                $stream = fopen($uploadedFile->getRealPath(), 'rb');
                if (!$stream) {
                    $m = "Could not open file ".$uploadedFile->getRealPath();
                    throw new \RuntimeException($m);
                }
                $this->filesystem->writeStream(
                    $entry->getFilePath().".".$uploadedFile->getClientOriginalExtension(),
                    $stream
                );
            } catch (FileExistsException $e) {
                throw new RuntimeException($e->getMessage());
            }
        }

        try {
            $this->filesystem->write(
                $entry->getFilePath().".json",
                $entry->asJson()
            );
        } catch (FileExistsException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
