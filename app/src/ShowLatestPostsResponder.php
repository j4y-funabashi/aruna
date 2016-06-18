<?php

namespace Aruna;

/**
 * Class ShowLatestPostsResponder
 * @author yourname
 */
class ShowLatestPostsResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "feed"
    ];

    public function feed()
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
            function ($post) {
                return $this->view->render(
                    "post_".$post->type().".html",
                    array("post" => $post)
                );
            },
            $this->payload->get("items")
        );
    }
}
