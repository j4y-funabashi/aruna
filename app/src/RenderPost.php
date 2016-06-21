<?php

namespace Aruna;

/**
 * Class RenderPost
 * @author yourname
 */
class RenderPost
{
    public function __construct($view)
    {
        $this->view = $view;
    }

    public function __invoke($post)
    {
        return $this->view->render(
            "post_".$post->type().".html",
            array("post" => $post)
        );
    }
}
