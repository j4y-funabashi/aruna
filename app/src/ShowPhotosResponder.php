<?php

namespace Aruna;

class ShowPhotosResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "feed"
    ];

    public function feed()
    {
        $posts = $this->renderPosts();
        $out = $this->view->render(
            "page_wrapper.html",
            array("body" => $this->renderPostGrid($posts, 3))
        );
        $this->response->setContent($out);
    }

    private function renderPosts()
    {
        return array_map(
            function ($post) {
                return $this->view->render(
                    "post_".$post->type.".html",
                    array("post" => $post)
                );
            },
            $this->payload->get("items")
        );
    }

    private function renderPostGrid($posts, $per_row)
    {
        $out = array();
        foreach (array_chunk($posts, $per_row) as $post_row) {
            $out[] = $this->view->render(
                "grid_row.html",
                array("row" => $post_row)
            );
        }
        return implode("", $out);
    }
}
