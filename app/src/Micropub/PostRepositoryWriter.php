<?php

namespace Aruna\Micropub;

use League\Flysystem\FileExistsException;
use RuntimeException;
use Ramsey\Uuid\Uuid;

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

    public function saveMediaFiles(array $files)
    {
        $out = [];
        foreach ($files as $file_key => $uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $out_path = sprintf(
                    "%s/%s.%s",
                    (new \DateTimeImmutable())->format("Y"),
                    Uuid::uuid4()->toString(),
                    strtolower($uploadedFile->getExtension())
                );
                $this->saveMediaFile($uploadedFile, $out_path);
                $out[$file_key][] = $out_path;
            }
        }
        return $out;
    }

    private function saveMediaFile($uploadedFile, $out_path)
    {
        try {
            $stream = fopen($uploadedFile->getRealPath(), 'rb');
            if (!$stream) {
                $m = "Could not open file ".$uploadedFile->getRealPath();
                throw new \RuntimeException($m);
            }
            $this->filesystem->writeStream(
                "media/".$out_path,
                $stream
            );
        } catch (FileExistsException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function savePost(NewPost $entry)
    {
        try {
            $this->filesystem->write(
                "posts/".$entry->getFilePath().".json",
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

    public function undelete(
        $post_id
    ) {
        $q = "UPDATE posts
            SET date_deleted = null
            WHERE id = :post_id";
        $r = $this->db->prepare($q);
        $data = [
            ":post_id" => $post_id
        ];
        $r->execute($data);
    }

    public function updatePostData(
        $post_id,
        $post_data
    ) {
        $q = "UPDATE posts
            SET post = :post_data
            WHERE id = :post_id";
        $r = $this->db->prepare($q);
        $data = [
            ":post_id" => $post_id,
            ":post_data" => json_encode($post_data)
        ];
        $r->execute($data);
    }
}
