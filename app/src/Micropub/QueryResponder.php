<?php

namespace Aruna\Micropub;

use Aruna\Responder;
use Symfony\Component\HttpFoundation\JsonResponse;

class QueryResponder extends Responder
{

    public function ok()
    {
        $this->response = new JsonResponse($this->payload->get("body"));
    }
}
