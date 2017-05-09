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
            ":source_html" => $mention["mention_source_html"]
        );

        $qry = "REPLACE INTO mentions (
            id,
            published,
            source,
            target,
            error,
            valid,
            source_html
        ) VALUES (
            :id,
            :published,
            :source,
            :target,
            :error,
            :valid,
            :source_html
        )";

        $res = $this->db->prepare($qry);
        $res->execute($mention_properties);
    }
}
