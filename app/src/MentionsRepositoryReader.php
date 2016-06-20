<?php

namespace Aruna;

/**
 * Class MentionsRepositoryReader
 * @author yourname
 */
class MentionsRepositoryReader
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findByPostId($post_id)
    {
        $q = "SELECT
            published,
            post_uid,
            author_name,
            author_photo,
            author_url,
            is_like,
            is_comment,
            content
            FROM mentions
            WHERE post_uid = :id
            ORDER BY uid ASC";
        $r = $this->db->prepare($q);
        $r->execute([":id" => $post_id]);

        $out = [];
        while ($mention = $r->fetch()) {
            $mention['content'] = htmlspecialchars(strip_tags($mention['content']), ENT_QUOTES);
            $mention['author_url'] = htmlspecialchars(strip_tags($mention['author_url']), ENT_QUOTES);
            $out[] = $mention;
        }


        return $out;
    }

    public function findLatest()
    {
        $q = "SELECT
            uid
            FROM mentions
            ORDER BY published DESC
            LIMIT 1;";
        $r = $this->db->prepare($q);
        $r->execute();
        $post = $r->fetch();
        return $post;
    }
}
