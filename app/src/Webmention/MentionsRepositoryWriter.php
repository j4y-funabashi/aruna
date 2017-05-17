<?php

namespace Aruna\Webmention;

class MentionsRepositoryWriter
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save($mention)
    {
        $mention_properties = array(
            ":id" => $mention["mention_id"],
            ":published" => $mention["published"],
            ":source" => $mention["source"],
            ":target" => $mention["target"],
            ":error" => $mention["error"],
            ":valid" => (string) $mention["valid"],
            ":author" => json_encode($mention["author"]),
            ":source_mf2" => json_encode($mention["mf2"]),
            ":type" => $mention["type"]
        );

        $qry = "REPLACE INTO mentions (
            id,
            published,
            source,
            target,
            error,
            valid,
            author,
            type,
            source_mf2
        ) VALUES (
            :id,
            :published,
            :source,
            :target,
            :error,
            :valid,
            :author,
            :type,
            :source_mf2
        )";

        $res = $this->db->prepare($qry);
        $res->execute($mention_properties);
    }
}
