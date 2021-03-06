<?php

namespace Aruna\Publish;

use \Aruna\PostViewModel;

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
        if (isset($post["sql_statements"])) {
            foreach ($post["sql_statements"] as $sql_statement) {
                $r = $this->db->prepare($sql_statement[0]);
                $r->execute($sql_statement[1]);
            }
            unset($post['sql_statements']);
        }

        $q = "REPLACE INTO posts (id, published, post, type)
            VALUES
            (:id, :published, :post, :type)";
        $r = $this->db->prepare($q);

        $post = new PostViewModel($post);

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
