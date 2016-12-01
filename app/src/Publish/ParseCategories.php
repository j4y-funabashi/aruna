<?php

namespace Aruna\Publish;

/**
 * Class ParseCategories
 * @author yourname
 */
class ParseCategories
{

    public function __invoke(array $post)
    {
        if (!isset($post["properties"]["category"])) {
            return $post;
        }
        $out = [];
        foreach ($post["properties"]["category"] as $cat) {
            $cat = array_map(
                "trim",
                explode(",", $cat)
            );
            $out = array_merge($out, $cat);
        }
        $post["properties"]["category"] = array_values(array_unique($out));

        return $post;
    }
}
