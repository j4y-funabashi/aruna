<?php

namespace Aruna\Action;

/**
 * Class CacheMentionToSql
 * @author yourname
 */
class CacheMentionToSql
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function __invoke($mention)
    {
        print "\n\n";
        var_dump(json_encode($mention['source_mf2_json']['items'][0]['properties']));
    }
}
