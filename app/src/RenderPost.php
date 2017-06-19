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

    public function __invoke(PostViewModel $post)
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

    private function renderPhoto(PostViewModel $post)
    {
        if (null === $post->get("photo")) {
            return null;
        }

        if (isset($post->get("photo")["alt"]) && isset($post->get("photo")["value"])) {
            return sprintf(
                '<img class="u-photo post--photo" alt="%s" src="%s" />',
                $post->get("photo")["alt"],
                $post->get("photo")["value"]
            );
        }

        $out = array_map(
            function ($photo) {
                return sprintf(
                    '<img class="u-photo post--photo" alt="photo" src="%s" />',
                    $this->getResizedPhoto($photo, "600")
                );
            },
            $post->photo()
        );
        return implode('', $out);
    }

    private function getResizedPhoto($photo, $size)
    {
        $photo_url = parse_url($photo);
        if ($photo_url["host"] != "media.j4y.co") {
            return $photo;
        }
        return str_replace($photo_url["path"], "/resized/".$size.$photo_url["path"], $photo);
    }

    private function renderUrl(PostViewModel $post)
    {
        return "/p/".$post->get('uid');
    }

    private function renderContent(PostViewModel $post)
    {
        if ($post->content()) {
            $content = $post->content();
            $markdown = new \cebe\markdown\GithubMarkdown();
            return sprintf(
                '<div class="p-name e-content">%s</div>',
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
        $out[] = '<div class="">';
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
