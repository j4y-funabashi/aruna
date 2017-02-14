<?php

namespace Aruna\Publish;

class CacheTags
{
    public function __construct(
        $tagsRepository
    ) {
    }

    public function __invoke(array $post)
    {
        if (!isset($post["properties"]["category"])) {
            return $post;
        }
        var_dump($post["properties"]["category"]);
        return $post;
    }
}
