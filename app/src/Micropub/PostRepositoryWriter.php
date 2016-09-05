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

    public function save(Post $entry, $files)
    {
        foreach ($files as $uploadedFile) {
            try {
                $stream = fopen($uploadedFile['real_path'], 'rb');
                if (!$stream) {
                    $m = "Could not open file ".$uploadedFile['real_path'];
                    throw new \RuntimeException($m);
                }
                $this->filesystem->writeStream(
                    $entry->getFilePath().".".$uploadedFile['original_ext'],
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
