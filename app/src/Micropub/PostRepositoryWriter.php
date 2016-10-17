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
    public function __construct(
        $filesystem,
        $db
    ) {
        $this->filesystem = $filesystem;
        $this->db = $db;
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

    public function delete(
        $post_id,
        $date_deleted
    ) {
        $q = "UPDATE posts
            SET date_deleted = :date_deleted
            WHERE id = :post_id";
        $r = $this->db->prepare($q);
        $data = [
            ":post_id" => $post_id,
            ":date_deleted" => $date_deleted
        ];
        $r->execute($data);
    }
}
