<?php

namespace Aruna;

/**
 * Class ShowMicropubFormResponder
 * @author yourname
 */
class ShowMicropubFormResponder extends Responder
{
    protected $payload_method = [
        "Aruna\Found" => "found"
    ];

    public function found()
    {

        $this->response->setContent(
            $this->view->render(
                'micropub.html',
                [
                    'current_date' => $this->payload->get('current_date'),
                    'access_token' => $this->payload->get('access_token')
                ]
            )
        );

        return $this->response;
    }
}
