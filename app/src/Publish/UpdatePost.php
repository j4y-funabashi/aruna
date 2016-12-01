<?php

namespace Aruna\Publish;

class UpdatePost
{

    public function __construct(
        $postsRepositoryReader,
        $postsRepositoryWriter,
        $applyUpdate
    ) {
        $this->postsRepositoryReader = $postsRepositoryReader;
        $this->postsRepositoryWriter = $postsRepositoryWriter;
        $this->applyUpdate = $applyUpdate;
    }

    public function __invoke($event)
    {
        foreach ($event["properties"]["url"] as $post_id) {
            $post_id = basename($post_id);
            $post = $this->postsRepositoryReader->fetchDataById($post_id);
            $updated = $this->applyUpdate->__invoke($post, $event["properties"]);
            $this->postsRepositoryWriter->updatePostData($post_id, $updated);
        }
        return $event;
    }
}
