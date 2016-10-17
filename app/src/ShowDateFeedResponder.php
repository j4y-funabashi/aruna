<?php

namespace Aruna;

/**
 * Class ShowDateFeedResponder
 * @author yourname
 */
class ShowDateFeedResponder extends Responder
{

    public function found()
    {
        $posts = $this->renderPosts();

        $out = $this->view->render(
            "page_wrapper.html",
            array(
                "page_title" => $this->payload->get("title"),
                "body" => $this->renderFeed($posts, "archive")
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
                "feed_title" => $this->payload->get("title"),
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
