<?php

namespace Aruna\Micropub;

use Aruna\Responder;

class UploadMediaResponder extends Responder
{

    public function unauthorized()
    {
        $this->response->setContent($this->payload->get("message"));
        $this->response->setStatusCode($this->response::HTTP_UNAUTHORIZED);
        return $this->response;
    }

    public function ok()
    {
        $headers = array(
            "Location" => $this->payload->get("location")
        );
        $this->response = $this->response::create(
            json_encode($this->payload->get("body")),
            $this->response::HTTP_CREATED,
            $headers
        );
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
