<?php

namespace Aruna\Micropub;

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
        $q = "REPLACE INTO posts (id, published, post)
            VALUES
            (:id, :published, :post)";
        $r = $this->db->prepare($q);

        $post_data = new \Aruna\Micropub\PostData();
        $r->execute(
            [
                ":id" => $post["uid"],
                ":published" => $post['published'],
                ":post" => json_encode($post_data->toMfArray($post))
            ]
        );

        return $post;
    }
}
