<?php

namespace Aruna\Micropub;

/**
 * Class AccessToken
 * @author yourname
 */
class AccessToken
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
            throw new \Exception("Token endpoint returned with status ".$response->getStatusCode());
        }
        if ($body['me'] !== $this->me) {
            throw new \Exception($body['me'] ." does not match ". $this->me);
        }
        if ($body['scope'] !== "post") {
            throw new \Exception("scope is not post");
        }

        return $body;
    }
}
