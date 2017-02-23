<?php

namespace Aruna\Reader;

use Aruna\Responder;

class ShowTaggedResponder extends Responder
{

    public function found()
    {
        $posts = $this->renderPosts();
        $out = $this->view->render(
            "page_wrapper.html",
            array(
                "page_title" => $this->payload->get("feed_title"),
                "body" => $this->renderFeed($posts, "photos")
            )
        );
        $this->response->setContent($out);
    }

    private function renderFeed(
        $posts,
        $title
    ) {
        return $this->view->render(
            "post_feed.html",
            array(
                "feed_title" => $this->payload->get("feed_title"),
                "items" => implode("\n", $posts),
                "nav_next" => $this->payload->get("nav_next")
            )
        );
    }

    private function renderPosts()
    {
        return array_map(
            array($this, "renderPost"),
            $this->payload->get("items")
        );
    }

}
