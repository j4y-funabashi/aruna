<?php

namespace Aruna;

class ShowPostResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "post"
    ];

    public function post()
    {
        $posts = $this->renderPosts();
        $out = $this->view->render(
            "page_wrapper.html",
            array(
                "page_title" => "photos",
                "body" => implode("\n", $posts)
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
                "feed_title" => "photos",
                "items" => $this->renderPostGrid($posts, 3),
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

    private function renderPostGrid($posts, $per_row)
    {
        $rows = array();
        foreach (array_chunk($posts, $per_row) as $post_row) {
            $rows[] = $this->view->render(
                "grid_row.html",
                array("row" => $post_row)
            );
        }
        return implode("", $rows);
    }
}
