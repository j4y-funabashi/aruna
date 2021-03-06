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

        $q = "CREATE TABLE IF NOT EXISTS tags (
                id,
                tag,
                PRIMARY KEY (id)
            );";
        $r = $this->exec($q);

        $q = "CREATE TABLE IF NOT EXISTS posts_tags (
                post_id,
                tag_id,
                PRIMARY KEY (post_id,tag_id)
            );";
        $r = $this->exec($q);

        $q = "DROP TABLE IF EXISTS event_log;";
        $r = $this->exec($q);
        $q = "CREATE TABLE IF NOT EXISTS event_log (
                id,
                type,
                version,
                data,
                PRIMARY KEY (id)
            );";
        $r = $this->exec($q);

        $q = "DROP TABLE IF EXISTS mentions;";
        $r = $this->exec($q);
        $q = "CREATE TABLE IF NOT EXISTS mentions (
            id,
            published,
            source,
            target,
            valid,
            type,
            error,
            author,
            source_mf2,
            PRIMARY KEY (id)
        );";
        $r = $this->exec($q);

        $q = "PRAGMA synchronous = off;";
        $r = $this->exec($q);
        $q = "PRAGMA temp_store = memory;";
        $r = $this->exec($q);
    }
}
