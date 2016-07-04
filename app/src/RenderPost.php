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
        $comments = $this->renderComments($post->comments());
        $likes = $this->renderLikes($post->likes());
        return $this->view->render(
            "post_".$post->type().".html",
            array(
                "post" => $post,
                "category" => $category,
                "comments" => $comments,
                "likes" => $likes
            )
        );
    }

    private function renderLikes($comments)
    {
        if (empty($comments)) {
            return "";
        }

        $out = array();
        foreach ($comments as $comment) {
            $out[] = '<div class="p-like h-cite">';

            // author
            $out[] = sprintf(
                '<a class="u-author h-card" href="%s">%s liked this</a>',
                $comment['properties']['author'][0]['properties']['url'],
                $comment['properties']['author'][0]['properties']['name']
            );
            $out[] = sprintf(
                '<a class="u-url" href="%s">
                    <time class="dt-published">%s</time>
                </a>',
                $comment['properties']['url'][0],
                $comment['properties']['published'][0]
            );


            $out[] = '</div>';
        }

        return implode("", $out);
    }

    private function renderComments($comments)
    {
        if (empty($comments)) {
            return "";
        }

        $out = array();
        foreach ($comments as $comment) {
            $out[] = '<div class="u-comment h-cite">';

            // author
            $out[] = sprintf(
                '<a class="u-author h-card" href="%s">%s</a>',
                $comment['properties']['author'][0]['properties']['url'],
                $comment['properties']['author'][0]['properties']['name']
            );
            // content
            $out[] = sprintf(
                '<p class="p-content p-name">%s</p>',
                $comment['properties']['content'][0]['value']
            );
            $out[] = sprintf(
                '<a class="u-url" href="%s">
                    <time class="dt-published">%s</time>
                </a>',
                $comment['properties']['url'][0],
                $comment['properties']['published'][0]
            );


            $out[] = '</div>';
        }

        return implode("", $out);
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
