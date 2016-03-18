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
        $event = $this->cacheTags($event);

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

    private function cacheTags($event)
    {
        if (false === isset($event['category'])) {
            return $event;
        }
        $event['category'] = (array) $event['category'];
        $event['category'] = array_filter($event['category'], 'strlen');
        if (empty($event['category'])) {
            return $event;
        }
        $event['category'] = array_map('strtolower', $event['category']);

        return $event;
    }
}
