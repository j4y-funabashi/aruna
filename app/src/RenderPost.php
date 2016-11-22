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
            "post_wrapper.html",
            array(
                "post" => $post,
                "url" => $this->renderUrl($post),
                "content" => $this->renderContent($post),
                "category" => $category,
                "comments" => $comments,
                "likes" => $likes
            )
        );
    }

    private function renderUrl($post)
    {
        return "/p/".$post->get('uid');
    }

    private function renderContent($post)
    {
        if ($post->get("content")) {
            $markdown = new \cebe\markdown\GithubMarkdown();
            return sprintf(
                '<div class="e-content">%s</div>',
                $markdown->parse($post->get("content"))
            );
        }
    }

    private function renderLikes($comments)
    {
        if (empty($comments)) {
            return "";
        }

        $out = array('<div class="post-meta">');
        foreach ($comments as $comment) {
            $out[] = '<span class="p-like h-cite">';

            // author
            $out[] = sprintf(
                '<a class="u-author h-card" href="%s"><img src="%s" class="author-photo" /></a>',
                $comment['properties']['author'][0]['properties']['url'],
                $comment['properties']['author'][0]['properties']['photo']
            );
            $out[] = '</span>';
        }
        $out[] = '</div>';

        return implode("", $out);
    }

    private function renderComments($comments)
    {
        if (empty($comments)) {
            return "";
        }

        $out = array('<div class="post-meta">');
        foreach ($comments as $comment) {
            $out[] = '<div class="u-comment h-cite">';

            $out[] = '<div class="post-comment">';
            // author
            $out[] = sprintf(
                '<a class="u-author h-card" href="%s"><img src="%s" class="author-photo" /></a>',
                $comment['properties']['author'][0]['properties']['url'],
                $comment['properties']['author'][0]['properties']['photo']
            );
            // content
            $out[] = sprintf(
                '<span class="p-content p-name">%s</span>',
                $comment['properties']['content'][0]['value']
            );
            $out[] = sprintf(
                '<a class="u-url" href="%s">
                    <time class="dt-published">%s</time>
                </a>',
                $comment['properties']['url'][0],
                $comment['properties']['published'][0]
            );
            $out[] = "</div>";


            $out[] = '</div>';
        }
        $out[] = '</div>';

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
