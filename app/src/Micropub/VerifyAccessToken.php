<?php

namespace Aruna\Micropub;

/**
 * Class VerifyAccessToken
 * @author yourname
 */
class VerifyAccessToken
{
    private $token_url;
    private $me;

    public function __construct(
        $http,
        $token_url,
        $me
    ) {
        $this->http = $http;
        $this->token_url = $token_url;
        $this->me = $me;
    }

    public function getTokenFromAuthCode($auth_code)
    {
        if (null === $auth_code) {
            throw new \Exception("Missing Authorization Header");
        }

        $response = $this->http->get(
            $this->token_url,
            [
                'headers' => [
                    'Authorization' => $auth_code,
                    'Content-type' =>  'application/x-www-form-urlencoded'
                ],
                'http_errors' => false
            ]
        );

        parse_str($response->getBody(), $body);

        if ($response->getStatusCode() !== 200) {
            $message = sprintf("Token endpoint returned with status [%s]", $response->getStatusCode());
            throw new \Exception($message);
        }
        if ($body['me'] !== $this->me) {
            $message = sprintf("Me value [%s] does not match %s", $body['me'], $this->me);
            throw new \Exception($message);
        }
        if ($body['scope'] !== "post") {
            throw new \Exception("scope is not post");
        }

        return $body;
    }
}
