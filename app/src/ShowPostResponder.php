<?php

namespace Aruna;

class ShowPostResponder extends Responder
{

    public function found()
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

    public function gone()
    {
        $posts = $this->renderPosts();
        $out = $this->view->render(
            "page_wrapper.html",
            array(
                "page_title" => "photos",
                "body" => implode("\n", $posts)
            )
        );
        $this->response->setStatusCode(
            $this->response::HTTP_GONE
        );
        $this->response->setContent($out);
    }

    private function renderPosts()
    {
        return array_map(
            array($this, "renderPost"),
            $this->payload->get("items")
        );
    }
}
