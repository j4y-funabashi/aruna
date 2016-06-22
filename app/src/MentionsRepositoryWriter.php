<?php

namespace Aruna;

/**
 * Class MentionsRepositoryWriter
 * @author yourname
 */
class MentionsRepositoryWriter
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save($mention_id, $post_id, $mention)
    {
        $mention_properties = array(
            ":uid" => $mention_id,
            ":published" => $mention->published(),
            ":post_uid" => $post_id,
            ":mention" => $mention->toJson()
        );

        $q = "REPLACE INTO mentions (
            id,
            published,
            post_id,
            mention
        ) VALUES (
            :uid,
            :published,
            :post_uid,
            :mention
        )";
        $r = $this->db->prepare($q);

        $r->execute(
            $mention_properties
        );
    }
}
