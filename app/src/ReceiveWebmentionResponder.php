<?php

namespace Aruna;

class ReceiveWebmentionResponder extends Responder
{

    public function badrequest()
    {
        $this->response->setStatusCode(400);
    }

    public function accepted()
    {
        $this->response->setStatusCode(202);
    }
}
