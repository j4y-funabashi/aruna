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
        $this->app_token = $token;
        $this->user_token = $user;
    }

    public function notify($message)
    {
        $this->log->notice($message);
        $res = $this->http->request(
            'POST',
            'https://api.pushover.net/1/messages.json',
            [
                'form_params' => [
                    'token' => $this->app_token,
                    'user' => $this->user_token,
                    'message' => $message
                ]
            ]
        );
    }
}
