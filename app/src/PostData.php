<?php

namespace Aruna;

class PostData
{
    public static function toMfArray($post_data)
    {
        $markdown = new \cebe\markdown\GithubMarkdown();
        $properties = array();

        // h
        $h = (isset($post_data['h']))
            ? $post_data['h']
            : "entry";
        unset($post_data['h']);

        // files
        if (isset($post_data['files'])) {
            foreach ($post_data['files'] as $file_key => $file_path) {
                $properties[$file_key] = array($file_path);
            }
            unset($post_data['files']);
        }

        // content
        if (isset($post_data['content'])) {
            $content = array(
                "value" => $post_data['content'],
                "html" => $markdown->parse($post_data['content'])
            );
            $post_data['content'] = $content;
        }

        // category
        if (isset($post_data['category']) && is_array($post_data['category'])) {
            $properties['category'] = $post_data['category'];
            unset($post_data['category']);
        }
        if (isset($post_data['category']) && !is_array($post_data['category'])) {
            $properties['category'] = array_filter(
                array_map(
                    "trim",
                    explode(",", $post_data['category'])
                )
            );
            unset($post_data['category']);
        }

        // all other properties
        foreach ($post_data as $k => $v) {
            $properties[$k] = array($v);
        }

        // build mfArray
        $converted = array(
            "type" => array("h-".$h),
            "properties" => $properties
        );
        $out = array(
            "items" => array($converted)
        );

        return $out;
    }
}
