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
        return $this->response;
    }

    public function unauthorized()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode(401);
        return $this->response;
    }
}
