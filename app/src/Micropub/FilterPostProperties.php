<?php

namespace Aruna\Micropub;

class FilterPostProperties
{

    public function __invoke(
        array $post,
        array $properties
    ) {
        if (!empty($properties)) {
            $post = ["properties" => array_intersect_key($post["properties"], array_flip($properties))];
        }
        return $post;
    }
}
