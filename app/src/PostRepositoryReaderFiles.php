<?php

namespace Aruna;

class PostRepositoryReaderFiles implements PostRepository
{
    public function listByType($post_type, $limit, $offset = 0)
    {
        $in_dir = getenv("ROOT_DIR")."/posts";
        $posts = array_filter(
            $this->list_files($in_dir),
            function ($post) use ($post_type) {
                return ($post->type() == $post_type);
            }
        );
        return array_slice($posts, $offset, $limit);
    }

    public function listByDate($year, $month, $day)
    {
        $query_data[":year"] = $year;
        $query_data[":month"] = $month;
        $query_data[":day"] = $day;

        $in_dir = getenv("ROOT_DIR")."/posts";

        $posts = array_filter(
            $this->list_files($in_dir),
            function ($post) use ($query_data) {

                $published = \DateTimeImmutable::createFromFormat(
                    "U",
                    strtotime($post->get("published"))
                );
                if (
                    $query_data[":year"] != "*"
                    && $published->format("Y") != $query_data[":year"]
                ) {
                    return false;
                }

                if (
                    $query_data[":month"] != "*"
                    && $published->format("m") != $query_data[":month"]
                ) {
                    return false;
                }

                if (
                    $query_data[":day"] != "*"
                    && $published->format("d") != $query_data[":day"]
                ) {
                    return false;
                }
                return true;
            }
        );
        $offset = 0;
        $limit = 30;
        return array_slice($posts, $offset, $limit);
    }

    public function findById($post_id)
    {
        $in_dir = getenv("ROOT_DIR")."/posts";
        $posts = array_filter(
            $this->list_files($in_dir),
            function ($post) use ($post_id) {
                return stripos($post->get("url"), $post_id);;
            }
        );
        return array_slice($posts, 0, 1);
    }

    private function list_files($in_dir) {
        $directory = new \RecursiveDirectoryIterator($in_dir);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = '/^.+\.html$/i';
        $files = new \RegexIterator($iterator, $regex, \RecursiveRegexIterator::GET_MATCH);

        $out = array();
        foreach ($files as $file) {
            $file_contents = file_get_contents($file[0]);
            $mf_array = \Mf2\parse($file_contents, "http://j4y.co");
            $out[] = new \Aruna\PostViewModel($mf_array);
        }

        usort(
            $out,
            function ($a, $b) {
                $al = strtolower($a->get("published"));
                $bl = strtolower($b->get("published"));
                if ($al == $bl) {
                    return 0;
                }
                return ($al < $bl) ? +1 : -1;
            }
        );

        return $out;
    }
}
