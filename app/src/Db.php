<?php

namespace Aruna;

class Db extends \PDO
{

    public function init()
    {
        $q = "CREATE TABLE IF NOT EXISTS posts (
                id,
                published,
                date_deleted,
                type,
                post,
                PRIMARY KEY (id)
            );";
        $r = $this->exec($q);

        $q = "CREATE TABLE IF NOT EXISTS event_log (
                id,
                type,
                version,
                data,
                PRIMARY KEY (id)
            );";
        $r = $this->exec($q);

        $q = "CREATE TABLE IF NOT EXISTS mentions (
            id,
            published,
            post_id,
            mention,
            PRIMARY KEY (id)
        );";
        $r = $this->exec($q);

        $q = "PRAGMA synchronous = off;";
        $r = $this->exec($q);
        $q = "PRAGMA temp_store = memory;";
        $r = $this->exec($q);
    }
}
