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
        $post_uid = $this->payload->get("post_uid");
        $this->response = $this->response::create(
            "",
            202,
            array("Location" => "http://j4y.co/p/".$post_uid)
        );
    }

    public function unauthorized()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode($this->response::HTTP_UNAUTHORIZED);
        return $this->response;
    }

    public function servererror()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode(
            $this->response::HTTP_INTERNAL_SERVER_ERROR
        );
        return $this->response;
    }
}
