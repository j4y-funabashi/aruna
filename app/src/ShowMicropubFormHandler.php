<?php

namespace Aruna;

/**
 * Class ShowMicropubFormHandler
 * @author yourname
 */
class ShowMicropubFormHandler implements Handler
{
    public function __construct($session)
    {
        $this->session = $session;
    }

    public function handle($command)
    {
        $data = [
            'current_date' => date('c'),
            'access_token' => $this->session->get('access_token')
        ];
        return new Found($data);
    }
}
