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
        foreach ($files as $file_key => $uploadedFile) {
            $out_path = sprintf(
                "%s/%s.%s",
                (new \DateTimeImmutable())->format("Y"),
                Uuid::uuid4()->toString(),
                strtolower($uploadedFile->getExtension())
            );
            $this->saveMediaFile($uploadedFile, $out_path);
            $out[$file_key] = $out_path;
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
                $out_path,
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

    public function updateReplace(
        $post_id,
        $properties
    ) {
        $post = $this->fetchPostByID($post_id);
        $post["properties"] = array_merge(
            $post["properties"],
            $properties
        );
        $this->updatePost(
            $post_id,
            json_encode($post)
        );
    }

    private function updatePost(
        $post_id,
        $post_data
    ) {
        $q = "UPDATE posts
            SET post = :post_data
            WHERE id = :post_id";
        $r = $this->db->prepare($q);
        $data = [
            ":post_id" => $post_id,
            ":post_data" => $post_data
        ];
        $r->execute($data);
    }

    private function fetchPostByID($post_id)
    {
        $q = "SELECT post FROM posts WHERE id = :post_id";
        $r = $this->db->prepare($q);
        $data = [
            ":post_id" => $post_id,
        ];
        $r->execute($data);
        $post = $r->fetch();
        return json_decode($post["post"], true);
    }
}
