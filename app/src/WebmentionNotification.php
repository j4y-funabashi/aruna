<?php

namespace Aruna;

class WebmentionNotification
{

    public function build(
        $post_view_model,
        $mention_view_model
    ) {
        switch ($mention_view_model->type()) {
            case 'reply':
                $action = "commented on your";
                break;
            case 'like':
                $action = "liked your";
                break;
            default:
                $action = "linked to your";
                break;
        }
        return sprintf(
            '%s %s %s "%s" [%s][%s]',
            $mention_view_model->author()['name'],
            $action,
            $post_view_model->type(),
            $post_view_model->get("content")["value"],
            $post_view_model->get("url"),
            $mention_view_model->get("url")
        );
    }
}
