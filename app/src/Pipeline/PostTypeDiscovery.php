<?php

namespace Aruna\Pipeline;

/**
 * Class PostTypeDiscovery
 * @author yourname
 */
class PostTypeDiscovery
{
    public function __construct()
    {
    }

    public function __invoke($event)
    {

        $event['post_type'] = $this->discoverPostType($event);
        return $event;
    }

    private function discoverPostType($event)
    {
        $post_type = "note";

        if (isset($event['files']['photo'])) {
            $post_type = "photo";
        } elseif (isset($event['bookmark-of'])) {
            $post_type = "bookmark";
        }

        return $post_type;
    }
}
