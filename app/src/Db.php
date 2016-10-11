<?php

namespace Aruna;

class Db extends \PDO
{

    public function init()
    {
        $q = "CREATE TABLE IF NOT EXISTS posts (
                id,
                published,
                type,
                post,
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
    }
}