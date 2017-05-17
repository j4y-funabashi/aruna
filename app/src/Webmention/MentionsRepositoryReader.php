<?php

namespace Aruna\Webmention;

class MentionsRepositoryReader
{

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function listMentions()
    {
        $q = "SELECT * FROM mentions ORDER BY published DESC";
        $r = $this->db->query($q);
        $out = [];
        while ($row = $r->fetch()) {
            $row["author"] = json_decode($row["author"], true);
            $row["source_mf2"] = json_decode($row["source_mf2"], true)["items"][0];
            $row["valid"] = ($row["valid"] == 1)
                ? "valid"
                : "invalid";
            $out[] = $row;
        }
        return $out;
    }
}
