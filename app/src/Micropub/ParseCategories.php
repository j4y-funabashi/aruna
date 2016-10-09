<?php

namespace Aruna\Micropub;

/**
 * Class ParseCategories
 * @author yourname
 */
class ParseCategories
{

    public function __invoke(array $post)
    {
        if (!isset($post["category"])) {
            return $post;
        }
        if (is_string($post['category'])) {
            $post['category'] = explode(",", $post['category']);
        }
        $new_categories = [];
        foreach ($post["category"] as $category) {
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
        $post["category"] = $new_categories;
        return $post;
    }
}
