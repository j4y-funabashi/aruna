<?php

namespace Aruna\Micropub;

use Aruna\PostViewModel;

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

    public function __invoke(array $post)
    {
        $post_data = new \Aruna\Micropub\PostData();
        $post = new PostViewModel($post_data->toMfArray($post));

        $q = "REPLACE INTO posts (id, published, post, type)
            VALUES
            (:id, :published, :post, :type)";
        $r = $this->db->prepare($q);

        $r->execute(
            [
                ":id" => $post->get("uid"),
                ":published" => $post->published(),
                ":post" => $post->toJson(),
                ":type" => $post->type()
            ]
        );

        return $post;
    }
}
