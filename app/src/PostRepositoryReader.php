<?php

namespace Aruna;

/**
 * Class PostRepositoryReader
 * @author yourname
 */
class PostRepositoryReader
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findById($post_id)
    {
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE id = :id";
        $r = $this->db->prepare($q);
        $r->execute([":id" => $post_id]);
        $post = $r->fetch();
        $post = json_decode($post['post'], true);
        return $post;
    }

    public function listFromId($from_id, $rpp)
    {
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE id > :id
            ORDER BY id DESC
            LIMIT :rpp";
        $r = $this->db->prepare($q);
        $r->execute(
            [
                ":id" => $from_id,
                ":rpp" => $rpp
            ]
        );

        $out = [];
        while ($post = $r->fetch()) {
            $out[] = json_decode($post['post'], true);
        }
        return $out;
    }
}
