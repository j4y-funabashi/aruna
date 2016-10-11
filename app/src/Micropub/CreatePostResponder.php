<?php

namespace Aruna\Micropub;

use Aruna\Responder;

/**
 * Class CreatePostResponder
 * @author yourname
 */
class CreatePostResponder extends Responder
{
    public function ok()
    {
        $post_url = $this->payload->get("items")[0]->getUid();
        $this->response = $this->response::create(
            "",
            202,
            array("Location" => "http://j4y.co/p/".$post_url)
        );
    }

    public function unauthorized()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode(401);
        return $this->response;
    }

    public function servererror()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode(500);
        return $this->response;
    }
}
