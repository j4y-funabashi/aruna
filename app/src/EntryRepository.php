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

    public function findById($post_id)
    {
        $post_filepath = array_shift(
            array_filter(
                $this->getJsonFilePaths(),
                function ($file_path) use ($post_id) {
                    return $file_path['filename'] == $post_id;
                }
            )
        );
        return $this->filesystem->read($post_filepath['path']);
    }

    private function getJsonFilePaths()
    {
        return array_filter(
            $this->filesystem->listContents('', true),
            function ($file_path) {
                return substr($file_path['path'], -4) == "json";
            }
        );
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
