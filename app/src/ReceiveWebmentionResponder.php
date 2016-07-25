<?php

namespace Aruna;

class ReceiveWebmentionResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "render",
        "Aruna\BadRequest" => "bad_request",
        "Aruna\Accepted" => "accepted"
    ];

    public function render()
    {
    }

    public function bad_request()
    {
        $this->response->setStatusCode(400);
    }

    public function accepted()
    {
        $this->response->setStatusCode(202);
    }
}
