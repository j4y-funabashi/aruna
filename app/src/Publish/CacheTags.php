<?php

namespace Aruna\Publish;

class CacheTags
{

    public function __invoke(array $post)
    {
        if (!isset($post["properties"]["category"])) {
            return $post;
        }
        $post["sql_statements"] = array_filter(
            array_merge(
                array_map (
                    function ($tag) {
                        return [
                            "REPLACE INTO tags (id, tag) VALUES (?, ?)",
                            [md5($tag), $tag]
                        ];
                    },
                    $post["properties"]["category"]
                ),
                array_map (
                    function ($tag) use ($post) {
                        return [
                            "REPLACE INTO posts_tags (post_id, tag_id) VALUES (?, ?)",
                            [$post["properties"]["uid"][0], md5($tag)]
                        ];
                    },
                    $post["properties"]["category"]
                )
            )
        );
        return $post;
    }
}
