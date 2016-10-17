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

        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE strftime('%Y', published) = :year
            AND date_deleted IS NULL
            ".implode("\n", $where)."
            AND date_deleted IS NULL
            ORDER BY published DESC, id DESC
            ";
        $r = $this->db->prepare($q);
        $r->execute($query_data);

        $out = [];
        while ($post = $r->fetch()) {
            $out[] = new \Aruna\PostViewModel(json_decode($post['post'], true));
        }

        return $out;
    }

    public function findById($post_id)
    {

        // current
        $q = "SELECT
            id,
            published,
            date_deleted,
            post
            FROM posts
            WHERE id = :id";
        $r = $this->db->prepare($q);
        $r->execute([":id" => $post_id]);
        $post = $r->fetch();
        if ($post === false) {
            return array();
        }
        $post = new \Aruna\PostViewModel(
            json_decode($post['post'], true),
            $post['date_deleted']
        );
        foreach ($this->findMentionsByPostId($post_id) as $mention) {
            if ($mention->type() == "reply") {
                $post->setComment($mention);
            }
        }
        foreach ($this->findMentionsByPostId($post_id) as $mention) {
            if ($mention->type() == "like") {
                $post->setLike($mention);
            }
        }
        $out = array($post);

        return $out;
    }

    private function findMentionsByPostId($post_id)
    {
        $out = [];
        // current
        $q = "SELECT
            *
            FROM mentions
            WHERE post_id = :id
            ORDER BY published ASC";
        $r = $this->db->prepare($q);
        $r->execute([":id" => $post_id]);
        while ($mention = $r->fetch()) {
            $mention = new \Aruna\PostViewModel(json_decode($mention['mention'], true));
            $out[] = $mention;
        }
        return $out;
    }

    public function findLatestId()
    {
        $q = "SELECT
            MAX(id) AS id
            FROM posts";
        $r = $this->db->prepare($q);
        $r->execute();
        $post = $r->fetch();
        if ($post === false) {
            return 0;
        }
        return $post["id"];
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

    public function listByType($post_type, $limit, $offset = 0)
    {
        $q = "SELECT
            id,
            published,
            post
            FROM posts
            WHERE type = :post_type
            AND date_deleted IS NULL
            ORDER BY published DESC
            LIMIT :offset,:limit";
        $r = $this->db->prepare($q);
        $r->execute(
            array(
                ":post_type" => $post_type,
                ":limit" => $limit,
                ":offset" => $offset
            )
        );

        $out = [];
        while ($post = $r->fetch()) {
            $out[] = new \Aruna\PostViewModel(json_decode($post['post'], true));
        }
        return $out;
    }
}
