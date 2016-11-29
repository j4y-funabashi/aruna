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
        if ($post->type() == "tombstone") {
            return $this->view->render(
                "post_tombstone.html",
                array(
                    "post" => $post,
                )
            );
        }

        return $this->view->render(
            "post_wrapper.html",
            array(
                "post" => $post,
                "url" => $this->renderUrl($post),
                "content" => $this->renderContent($post),
                "category" => $this->renderCategory($post->category()),
                "photo" => $this->renderPhoto($post)
            )
        );
    }

    private function renderPhoto($post)
    {
        if (null === $post->get("photo")) {
            return null;
        }

        return sprintf(
            '<img class="u-photo post-photo" alt="photo" src="%s" />',
            $this->getPhoto($post)
        );
    }

    private function getPhoto($post)
    {
        $photo_url = parse_url($post->get("photo"));
        if (!isset($photo_url["host"])) {
            return "/".$post->get("photo");
        }
        return $post->get("photo");
    }

    private function renderUrl($post)
    {
        return "/p/".$post->get('uid');
    }

    private function renderContent($post)
    {
        if ($post->get("content")) {
            $content = $post->get("content");
            if (isset($content["html"])) {
                $content = $content["html"];
            }
            $markdown = new \cebe\markdown\GithubMarkdown();
            return sprintf(
                '<div class="e-content">%s</div>',
                $markdown->parse($content)
            );
        }
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
