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

    public function listByDate($year, $month, $day)
    {
        $where = array();
        $query_data = [
            ":year" => $year
        ];
        if ($month != "*") {
            $where[] = "AND strftime('%m', published) = :month";
            $query_data[":month"] = $month;
        }
        if ($day != "*") {
            $where[] = "AND strftime('%d', published) = :day";
            $query_data[":day"] = $day;
        }

        // get previousious
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE strftime('%Y', published) = :year
            ".implode("\n", $where)."
            ORDER BY published DESC, id DESC
            ";
        $r = $this->db->prepare($q);
        $r->execute($query_data);

        $out = [];
        while ($post = $r->fetch()) {
            $out[] = json_decode($post['post'], true);
        }

        return $out;
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
            strftime('%Y', published) as year,
            strftime('%m', published) as month,
            count(*) as count
            FROM posts
            GROUP BY strftime('%Y', published), strftime('%m', published)
            ORDER BY published DESC";
        $r = $this->db->prepare($q);
        $r->execute();

        $out = [];
        while ($post = $r->fetch()) {
            $post['human'] = date("M Y", strtotime($post['year']."-".$post['month']."-01"));
            $post['link'] = date("Y/m", strtotime($post['year']."-".$post['month']."-01"));
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
