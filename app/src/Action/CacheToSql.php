<?php

namespace Aruna\Action;

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

    public function __invoke($event)
    {

        $q = "REPLACE INTO posts (id, published, post)
            VALUES
            (:id, :published, :post)";
        $r = $this->db->prepare($q);

        $data = [
            ":id" => $event['uid'],
            ":published" => $event['published'],
            ":post" => json_encode($event)
        ];

        $r->execute(
            $data
        );
    }
}
