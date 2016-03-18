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
        if (false === isset($mention['source_mf2_json'])) {
            return $mention;
        }

        $mf2 = $mention['source_mf2_json'];
        $mention_properties = $this->parseMentionProperties($mf2);

        $mention_properties[':published'] = $mention['published'];
        $mention_properties[':uid'] = $mention['uid'];
        $mention_properties[':post_uid'] = $mention['target_uid'];

        $q = "REPLACE INTO mentions (
            uid,
            published,
            post_uid,
            author_name,
            author_photo,
            author_url,
            is_like,
            is_comment,
            content
        ) VALUES (
            :uid,
            :published,
            :post_uid,
            :author_name,
            :author_photo,
            :author_url,
            :is_like,
            :is_comment,
            :content
        )";
        $r = $this->db->prepare($q);

        $r->execute(
            $mention_properties
        );
    }

    private function parseMentionProperties($mention)
    {
        $out = [];

        // filter to entries
        $entries = array_filter($mention['items'], function ($item) {
            return (isset($item['type']) && in_array('h-entry', $item['type']));
        });
        $entry = $entries[0];

        // is there an author in the entry?
        if (isset($entry['properties']['author'])) {
            $out[':author_name'] = $entry['properties']['author'][0]['properties']['name'][0];
            $out[':author_photo'] = $entry['properties']['author'][0]['properties']['photo'][0];
            $out[':author_url'] = $entry['properties']['author'][0]['properties']['url'][0];
        }

        $out[':is_like'] = isset($entry['properties']['like-of']);
        $out[':is_comment'] = isset($entry['properties']['in-reply-to']);

        if (isset($entry['properties']['content'])) {
            $out[':content'] = $entry['properties']['content'][0]['value'];
        }

        return $out;
    }
}
