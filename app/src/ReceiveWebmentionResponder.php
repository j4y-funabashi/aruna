<?php

namespace Aruna;

class ReceiveWebmentionResponder extends Responder
{

    public function badrequest()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode(
            $this->response::HTTP_BAD_REQUEST
        );
    }

    public function accepted()
    {
        $this->response->setStatusCode(
            $this->response::HTTP_ACCEPTED
        );
    }
}
