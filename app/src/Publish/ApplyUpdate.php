<?php

namespace Aruna\Publish;

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
        if (isset($update["delete"])) {
            $post = $this->delete($post, $update["delete"]);
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

    private function delete(
        $post,
        $new_properties
    ) {
        foreach ($new_properties as $k => $v) {
            if (is_string($v) && isset($post["properties"][$v])) {
                unset($post["properties"][$v]);
            }
        }
        foreach ($new_properties as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $l => $b) {
                    if (is_string($b)) {
                        unset($post["properties"][$k][array_search($b, $post["properties"][$k])]);
                    }
                }
            }
        }
        return $post;
    }
}
