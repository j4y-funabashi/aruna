<?php

namespace Aruna\Pipeline;

use Aruna\PostViewModel;

/**
 * Class ParseCategories
 * @author yourname
 */
class ParseCategories
{

    public function __invoke(PostViewModel $post)
    {
        $new_categories = [];
        foreach ($post->category() as $category) {
            $category = trim($category);
            if (is_string($category) && substr($category, 0, 1) == "@") {
                $category = array(
                    "type" => ["h-card"],
                    "properties" => [
                        "name" => [$category],
                        "url" => [substr($category, 1)]
                    ]
                );
            }
            $new_categories[] = $category;
        }
        $post->setCategory($new_categories);
        return $post;
    }
}
