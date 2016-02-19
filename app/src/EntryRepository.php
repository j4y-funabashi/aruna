<?php

namespace Aruna;

use League\Flysystem\FileExistsException;
use RuntimeException;

/**
 * Class EntryRepository
 * @author yourname
 */
class EntryRepository
{
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(Post $entry, $files)
    {
        try {
            $this->filesystem->write(
                $entry->getFilePath()."_".$entry->getUid().".json",
                $entry->asJson()
            );
        } catch (FileExistsException $e) {
            throw new RuntimeException($e->getMessage());
        }

        foreach ($files as $uploadedFile) {
            try {
                $stream = fopen($uploadedFile->getRealPath(), 'r+');
                $this->filesystem->writeStream(
                    $entry->getFilePath()."_".$entry->getUid().".".$uploadedFile->getClientOriginalExtension(),
                    $stream
                );
            } catch (FileExistsException $e) {
                throw new RuntimeException($e->getMessage());
            }
        }
    }
}
