<?php

namespace Aruna;

class NotifyService
{
    public function __construct(
        $http,
        $log,
        $token,
        $user
    ) {
        $this->http = $http;
        $this->log = $log;
        $this->token = $token;
        $this->user = $user;
    }

    public function notify($message)
    {
        $this->log->notice($message);
        $res = $this->http->request(
            'POST',
            'https://api.pushover.net/1/messages.json',
            [
                'form_params' => [
                    'token' => 'asf6pqff2y93zw4698wka5peb3r77p',
                    'user' => "uy9ob2vuar46pzmvf2gcur624b33fb",
                    'message' => $message
                ]
            ]
        );
    }
}
