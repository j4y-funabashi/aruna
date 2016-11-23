<?php

namespace Aruna\Micropub;

/**
 * Class CacheToSql
 * @author yourname
 */
class CacheToSql
{
    public function __construct(
        $db
    ) {
        $this->db = $db;
    }

    public function __invoke(array $post)
    {
        $q = "REPLACE INTO posts (id, published, post, type)
            VALUES
            (:id, :published, :post, :type)";
        $r = $this->db->prepare($q);

        $post = new \Aruna\PostViewModel($post);

        $r->execute(
            [
                ":id" => $post->get("uid"),
                ":published" => $post->get("published"),
                ":post" => $post->toJson(),
                ":type" => $post->type()
            ]
        );

        return $post;
    }
}
