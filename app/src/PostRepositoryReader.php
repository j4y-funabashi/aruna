<?php

namespace Aruna;

/**
 * Class PostRepositoryReader
 * @author yourname
 */
class PostRepositoryReader
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findById($post_id)
    {

        // current
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE id = :id";
        $r = $this->db->prepare($q);
        $r->execute([":id" => $post_id]);
        $current = $r->fetch();
        $out['current'] = json_decode($current['post'], true);

        // get previousious
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE published <= :published
            AND id < :id
            ORDER BY published DESC, id DESC
            LIMIT 1";
        $r = $this->db->prepare($q);
        $r->execute(
            [
                ":id" => $post_id,
                ":published" => $current['published']
            ]
        );
        $previous = $r->fetch();
        $out['previous'] = json_decode($previous['post'], true);

        // get next
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE published >= :published
            AND id > :id
            ORDER BY published ASC, id ASC
            LIMIT 1";
        $r = $this->db->prepare($q);
        $r->execute(
            [
                ":id" => $post_id,
                ":published" => $current['published']
            ]
        );
        $next = $r->fetch();
        $out['next'] = json_decode($next['post'], true);

        return $out;
    }

    public function findLatest()
    {
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            ORDER BY id DESC
            LIMIT 1;";
        $r = $this->db->prepare($q);
        $r->execute();
        $post = $r->fetch();
        $post = json_decode($post['post'], true);
        return $post;
    }

    public function listMonths()
    {
        $q = "SELECT
            strftime('%Y-%m-01', published) as month,
            count(*) as count
            FROM posts
            ORDER BY published DESC";
        $r = $this->db->prepare($q);
        $r->execute();

        $out = [];
        while ($post = $r->fetch()) {
            $post['human'] = date("M Y", strtotime($post['month']));
            $post['link'] = date("Y/m", strtotime($post['month']));
            $out[] = $post;
        }

        return $out;
    }

    public function listFromId($from_id, $rpp)
    {
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE id > :id
            ORDER BY published DESC
            LIMIT :rpp";
        $r = $this->db->prepare($q);
        $r->execute(
            [
                ":id" => $from_id,
                ":rpp" => $rpp
            ]
        );

        $out = [];
        while ($post = $r->fetch()) {
            $out[] = json_decode($post['post'], true);
        }
        return $out;
    }
}
