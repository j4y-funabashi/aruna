<?php

namespace Aruna;

/**
 * Class ShowLatestPostsResponder
 * @author yourname
 */
class ShowLatestPostsResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "feed"
    ];

    public function feed()
    {

        $this->response->setContent(
            $this->view->render(
                'feed.html',
                [
                    'posts' => $this->payload->get('items'),
                    'feed_nav' => $this->payload->get('nav')
                ]
            )
        );
    }
}
