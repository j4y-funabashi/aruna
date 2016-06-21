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
        $category = $this->renderCategory($post->category());
        return $this->view->render(
            "post_".$post->type().".html",
            array(
                "post" => $post,
                "category" => $category
            )
        );
    }

    private function renderCategory($category)
    {
        if ($category == null) {
            return "";
        }
        $out = array();
        $tags = array();
        $out[] = '<div class="post-tags">';
        foreach ($category as $cat) {
            if (is_string($cat)) {
                $tags[] = sprintf('<a class="p-category" href="/tag/%s">%s</a>', $cat, $cat);
            }
            if (is_array($cat) && $cat['type'][0] == "h-card") {
                $tags[] = sprintf('<a class="p-category h-card" href="%s">%s</a>', $cat['properties']['url'][0], $cat['properties']['name'][0]);
            }
        }
        $out[] = implode(" ", $tags);
        $out[] = '</div>';
        return implode("", $out);
    }
}
