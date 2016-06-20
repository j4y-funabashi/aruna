<?php

namespace Aruna\Action;

/**
 * Class CacheToSql
 * @author yourname
 */
class CacheToSql
{
    public function __construct(
        $log,
        $db
    ) {
        $this->log = $log;
        $this->db = $db;
    }

    public function __invoke($post)
    {
        $q = "REPLACE INTO posts (id, published, type, post)
            VALUES
            (:id, :published, :type, :post)";
        $r = $this->db->prepare($q);

        $data = [
            ":id" => basename($post->get("url")),
            ":published" => $post->get('published'),
            ":post" => $post->toJson(),
            ":type" => $post->type()
        ];

        $r->execute(
            $data
        );

        return $post;
    }
}
