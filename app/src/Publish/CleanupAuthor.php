<?php

namespace Aruna\Publish;

class CleanupAuthor
{
    public function __invoke(array $post)
    {
        if (isset($post["properties"]["author"])) {
            return $post;
        }
        $author = [
            "https://j4y.co/"
        ];
        $post['properties']['author'] = $author;
        return $post;
    }
}
