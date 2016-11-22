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

        $r->execute(
            [
                ":id" => $post["properties"]["uid"][0],
                ":published" => $post["properties"]["published"][0],
                ":post" => json_encode($post),
                ":type" => "note"
            ]
        );

        return $post;
    }
}
