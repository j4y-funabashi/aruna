<?php

namespace Aruna\Micropub;

class AccessToken
{

    public function __construct(
        array $body
    ) {
        if ($body['me'] !== $this->me) {
            throw new \Exception("Me value [".$body['me']."] does not match ". $this->me);
        }
        if ($body['scope'] !== "post") {
            throw new \Exception("scope is not post");
        }
    }
}
