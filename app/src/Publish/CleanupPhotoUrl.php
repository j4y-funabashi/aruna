<?php

namespace Aruna\Publish;

class CleanupPhotoUrl
{
    public function __construct(
        $media_endpoint
    ) {
        $this->media_endpoint = $media_endpoint;
    }

    public function __invoke(array $post)
    {
        if (!isset($post["properties"]["photo"])) {
            return $post;
        }
        foreach ($post["properties"]["photo"] as $k => $photo) {
            if (is_string($photo)) {
                $post["properties"]["photo"][$k] = $this->getPhotoUrl($photo);
            }
        }
        return $post;
    }

    private function getPhotoUrl($photo)
    {
        $photo_url = parse_url($photo);
        if (!isset($photo_url["host"])) {
            return trim($this->media_endpoint, "/")."/".$photo;
        }
        return $photo;
    }
}
