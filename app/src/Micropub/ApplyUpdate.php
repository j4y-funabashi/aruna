<?php

namespace Aruna\Micropub;

class ApplyUpdate
{

    public function __invoke(
        $post,
        $update
    ) {
        if (isset($update["replace"])) {
            $post = $this->replace($post, $update["replace"]);
        }
        if (isset($update["add"])) {
            $post = $this->add($post, $update["add"]);
        }
        return $post;
    }

    private function replace(
        $post,
        $new_properties
    ) {
        $post["properties"] = array_merge(
            $post["properties"],
            $new_properties
        );
        return $post;
    }

    private function add(
        $post,
        $new_properties
    ) {
        foreach ($new_properties as $k => $v) {
            if (!isset($post["properties"][$k])) {
                $post["properties"][$k] = $v;
            } else {
                $post["properties"][$k] = array_merge($post["properties"][$k], $v);
            }
        }
        return $post;
    }
}
